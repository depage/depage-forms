/**
 * @require framework/shared/jquery-1.8.3.js
 *
 * gpl2 
 *
 * based on 
 * - uEditor (http://www.upian.com/upiansource/ueditor/en) by Denis Hovart 
 * - widgEditor (http://www.themaninblue.com/experiment/widgEditor) by Cameron Adams
 * - extended by Frank Hellenkamp
 *  
 * the markup is much better and more consistent than with jwysiwyg e.g.
 *
 * @author Frank Hellenkamp
 * @author Cameron Adams
 * @author Denis Hovart 
 */
(function($){
    // {{{ jquery.removeDuplicate()
    /* custom function to remove duplicate items from an array */
    $.removeDuplicate = function(array) {
        if(!(array instanceof Array)) return;
        label:for(var i = 0; i < array.length; i++ ) {  
            for(var j=0; j < array.length; j++ ) {
                if(j == i) continue;
                if(array[j] == array[i]) {
                    array = array.slice(j);
                    continue label;
                }
            }
        }
        return array;
    }
    // }}}
    // {{{ ':inline'-selector for inline elements
    /* new selector to check if the tags submitted are inline elements */
    $.extend($.expr[':'],{
        inline: function(element) {
            return (
                $(element).is('a') ||
                $(element).is('em') ||
                $(element).is('i') ||
                $(element).is('font') ||
                $(element).is('span') ||
                $(element).is('strong') ||
                $(element).is('b') ||
                $(element).is('u')
            );
        }
    });
    // }}}
    
    // {{{ depageEditor class
    // depageEditor class
    var depageEditor = function(element, settings) {
        $.extend(this, {
            // {{{ settings
            settings : settings,
            // }}}
            // {{{ createDOM()
            createDOM : function() {
                this.textarea = element;
                this.container = document.createElement("div");
                this.iframe = document.createElement("iframe");
                this.input = document.createElement("input");
                this.stylesheetLink = document.createElement("a");

                // make stylesheet an absolute url
                this.settings.stylesheet = $(this.stylesheetLink).attr({
                    href : this.settings.stylesheet
                })[0].href;

                $(this.input).attr({
                    type : 'hidden',
                    name : $(this.textarea).attr('name'),
                    value : $(this.textarea).attr('value') // old textarea value
                });

                $(this.textarea).addClass('depageEditorTextarea');
                $(this.textarea).attr('name', $(this.textarea).attr('name') + "depageEditorTextarea");
                $(this.textarea).hide();

                $(this.container).addClass(settings.containerClass);
                $(this.iframe).addClass('depageEditorIframe');

                this.toolbar = new depageEditorToolbar(this);
                $(this.container).append(this.toolbar.itemsList);
                $(this.container).append(this.iframe);
                $(this.container).append(this.input);
                $(this.container).hide();

                this.input.depageEditorObject = this;
                $(this.textarea).replaceWith(this.container);
            },
            // }}}
            // {{{ writeDocument()
            writeDocument : function() {
                /* HTML template into which the HTML Editor content is inserted */
                var documentTemplate = '\
                    <html>\
                        <head>\
                            <link rel="stylesheet" type="text/css" href="' + settings.stylesheet + '"></link>\
                        </head>\
                        <body id="iframeBody">\
                            ' + $(this.input).val() + '\
                        </body>\
                    </html>\
                ';
                
                //documentTemplate = documentTemplate.replace(/INSERT:STYLESHEET:END/, '<link rel="stylesheet" type="text/css" href="' + settings.stylesheet + '"></link>');
                documentTemplate = documentTemplate.replace(/INSERT:CONTENT:END/, $(this.input).val());

                this.iframe.contentWindow.document.open();
                this.iframe.contentWindow.document.write(documentTemplate);
                this.iframe.contentWindow.document.close();

                var self = this;
                $(this.iframe).load( function() {
                    self.autogrow();
                });
            },
            // }}}
            // {{{ makeEditable()
            makeEditable : function() {
                var self = this;
                try {
                    this.iframe.contentWindow.document.designMode = "on";
                } catch (e) {
                    /* setTimeout needed to counteract Mozilla bug whereby you can't immediately change designMode on newly created iframes */
                    setTimeout((  function(){ self.makeEditable() }), 250);
                    return false;
                }

                $(this.container).show();
                $(this.textarea).show();
                $(this.iframe.contentWindow.document).mouseup(function() { 
                    self.toolbar.checkState(self);
                    self.autogrow();
                }).keyup(function() { 
                    self.toolbar.checkState(self);
                    self.autogrow();
                }).keydown(function(e){ 
                    self.detectPaste(e); 
                    self.autogrow();
                }).scroll(function() { 
                    self.autogrow();
                });
                this.locked = false;
            },
            // }}}
            // {{{ focusEditor()
            focusEditor : function() {
                if ($.browser.msie) {
                } else {
                    this.iframe.focus();
                }
            },
            // }}}
            // {{{ styleWithCSS()
            styleWithCSS : function() {
                try {
                    this.iframe.contentWindow.document.execCommand("styleWithCSS", false, false);
                } catch (e) {
                    try {
                        this.iframe.contentWindow.document.execCommand("useCSS", 0, true);
                    } catch (e) {
                    }
                }
            },
            // }}}
            // {{{ modifyFormSubmit()
            modifyFormSubmit : function() {
                var self = this;
                var form = $(this.container).parents('form');
                form.submit(function() {
                    return self.updateDepageEditorInput();
                });
            },
            // }}}
            // {{{ insertNewParagraph()
            insertNewParagraph : function(elementArray, succeedingElement) {
                var body = $(this.iframe).contents().find('body');
                var paragraph = this.iframe.contentWindow.document.createElement("p");
                $(elementArray).each(function(){
                    $(paragraph).append(this);
                });
                body.append(paragraph);
            },
            // }}}
            // {{{ paragraphise()
            paragraphise : function() {
                if (settings.insertParagraphs && this.wysiwyg) {
                    var bodyNodes = $(this.iframe).contents().find('body').contents();

                    /* Remove all text nodes containing just whitespace */
                    bodyNodes.each(function() {
                        // something like $(this).is('#text')); would be great
                        if (this.nodeName.toLowerCase() == "#text" &&
                            this.data.search(/^\s*$/) != -1) {
                            this.data = '';
                        }
                    });
                    
                    var self = this;
                    var removedElements = new Array();

                    bodyNodes.each(function() {
                        if($(this).is(':inline') || this.nodeType == 3) {
                            removedElements.push(this);
                            $(this).remove();
                        }
                        else if($(this).is('br')) {
                            if(!$(this).is(':last-child')) {
                                /* If the current break tag is followed by another break tag  */
                                if($(this).next().is('br')) {
                                    /* Remove consecutive break tags  */
                                    while($(this).next().is('br')) {
                                        $(this).remove();
                                    }
                                    if (removedElements.length) {
                                        self.insertNewParagraph(removedElements, this);
                                        removedElements = new Array();
                                    }
                                }
                                /* If the break tag appears before a block element */
                                else if (!$(this).is(':inline')  && this.nodeType != 3) {
                                    $(this).remove();
                                }
                                else if (removedElements.length) {
                                    removedElements.push(this.cloneNode(true));
                                    $(this).remove();
                                }
                                else {
                                    $(this).remove();
                                }
                            }
                        }
                        else if (removedElements.length) {
                            self.insertNewParagraph(removedElements, this);
                            removedElements = new Array();
                        }

                    });

                    if (removedElements.length > 0)
                    {
                        this.insertNewParagraph(removedElements);
                    }
                }
            },
            // }}}
            // {{{ switchMode()
            switchMode : function() {
                if (!this.locked) {
                    this.locked = true;
                    
                    /* Switch to HTML source */
                    if (this.wysiwyg) {
                        this.updateDepageEditorInput();
                        $(this.textarea).val($(this.input).val());
                        $(this.iframe).replaceWith(this.textarea);
                        this.toolbar.disable();
                        this.wysiwyg = false;
                        this.locked = false;
                    }
                    /* Switch to WYSIWYG */
                    else {
                        this.updateDepageEditorInput();
                        $(this.textarea).replaceWith(this.iframe);
                        this.writeDocument(this.input.value);
                        this.toolbar.enable();
                        this.makeEditable();
                        this.wysiwyg = true;
                    }
                }
            },
            // }}}
            // {{{ detectPaste()
            detectPaste : function(e) {
                if ((e.ctrlKey && e.keyCode == 86) && !this.cleaning) {
                    var self = this;
                    setTimeout(function(e){
                        self.cleanSource();
                    }, 100);
                }
                if (e.keyCode == 13) {
                    // replace textnode inside of body with paragraphs
                    var selection = this.getSelection();
                    var parentnode = selection.parentnode;

                    while (parentnode[0].nodeType == 3 || parentnode.is(':inline')) { // textNode
                        parentnode = parentnode.parent();
                    }

                    var nodename = parentnode[0].nodeName.toLowerCase();

                    if (nodename == "body" || nodename == "div") {
                        var tag = $.browser.msie ? "<p>" : "p";
                        this.styleWithCSS();
                        this.iframe.contentWindow.document.execCommand('FormatBlock', false, tag);
                    }
                }
            },
            // }}}
            // {{{ cleanSource()
            cleanSource : function() {
                this.cleaning = true;
                var html = "";
                var body = $(this.iframe.contentWindow.document).find("body");
                
                body.find("*").each(function() {
                    var tagName = this.nodeName.toLowerCase();

                    if ($.inArray(tagName, settings.allowedTags) === -1) {
                        var action = settings.undesiredTags[tagName];

                        if (action == "remove") {
                            $(this).remove();
                        } else {
                            var parentTag = $(this);
                            var targetTagName = parentTag.parent()[0].tagName.toLowerCase();

                            if (targetTagName == "body" && this.firstChild && ($(this.firstChild).is(':inline') || this.firstChild.nodeType == 3)) {
                                // when target-element is body then wrap text- and inline-elements in additional paragraph
                                var newP = $("<p></p>").insertBefore(parentTag);
                                parentTag.contents().each(function() {
                                    newP.append(this);
                                });
                            } else {
                                parentTag.contents().each(function() {
                                    parentTag.before(this);
                                });
                            }
                            parentTag.remove();
                        }
                    }
                });

                if (this.wysiwyg) {
                    html = body.html();
                } else {
                    html = $(this.textarea).val();
                }

                /* Remove leading and trailing whitespace */
                html = html.replace(/^\s*/, "");
                html = html.replace(/\s*$/, "");

                /* remove comments */
                html = html.replace(/<--.*-->/, "");
                
                /* format content inside html tags */
                html = html.replace(/<[^>]*>/g, function(match) {
                    /* replace single quotes */
                    match = match.replace(/='(.*)' /g, '="$1" ');
                    /* check if the attribute is allowed */

                    match = match.replace(/ ([^=]+)="?([^"]*)"?/g, function(match, attribute, value){
                        if( $.inArray(attribute, settings.allowedAttributes) == -1) return '';
                        switch(attribute) {
                            case 'id' :
                                if($.inArray(value, settings.allowedIDs) == -1) return '';
                            case 'class' :
                                if($.inArray(value, settings.allowedClasses) == -1) return '';                            
                            default :
                                return match;
                        }
                    });
                    return match.toLowerCase();
                });

                /* Remove style attribute inside any tag */
                html = html.replace(/ style="[^"]*"/g, "");
                /* Replace improper BRs */
                html = html.replace(/<br>/g, "<br />");
                /* Remove BRs right before the end of blocks */
                html = html.replace(/<br \/>\s*<\/(h1|h2|h3|h4|h5|h6|li|p)/g, "</$1");
                /* Shift the <br /> at the end of an inline element just after it */
                html = html.replace(/(<br \/>)*\s*(<\/[^>]*>)/g, "$2$1");
/*
                // Remove BRs alone in tags
                html = html.replace(/<[^\/>]*>(<br \/>)*\s*<\/[^>]*>/g, "$1");
*/    
                /* Replace improper IMGs */
                html = html.replace(/(<img [^>]+[^\/])>/g, "$1 />");
                /* Remove empty tags */
                html = html.replace(/(<[^\/]>|<[^\/][^>]*[^\/]>)\s*<\/[^>]*>/g, "");
                /* Final cleanout for MS Word cruft */
                html = html.replace(/<\?xml[^>]*>/g, "");
                html = html.replace(/<[^ >]+:[^>]*>/g, "");
                html = html.replace(/<\/[^ >]+:[^>]*>/g, "");

                // remove newlines and add new ones after block-level elements
                html = html.replace(/\n/g, "");
                html = html.replace(/(<\/(p|h1|h2|li|ul|ol)>)/g, "$1\n");

                if (this.wysiwyg) {
                    $(this.iframe.contentWindow.document).find("body").html(html);
                } else {
                    $(this.textarea).val(html);
                }
                
                $(this.input).val(html);
                this.cleaning = false;
            },
            // }}}
            // {{{ refreshDisplay()
            refreshDisplay : function() {
                if (this.wysiwyg) {
                    $(this.iframe.contentWindow.document).find("body").html($(this.input).val());
                } else {
                    $(this.textarea).val($(this.input).val());
                }
            },
            // }}}
            // {{{ autogrow()
            autogrow : function() {
                if (this.settings.autogrow == false) {
                    // don't autogrow
                    return;
                }
                if (this.wysiwyg) {
                    var extraSpace = 30;
                    var innerBody = $(this.iframe.contentWindow.document.body);
                    var toolbarList = $(this.toolbar.itemsList);
                    var newHeight = 0;
                    if (innerBody[0] == undefined) {
                        return;
                    }

                    var offset = Math.max(0, $(window).scrollTop() - $(this.iframe).offset().top);
                    offset = Math.min(offset, $(this.iframe).height() - toolbarList.height());

                    toolbarList.css({
                        top: offset
                    });

                    var lastElement = innerBody.children(":last");
                    if (lastElement.length > 0) {
                        newHeight = lastElement.offset().top + lastElement.height() + extraSpace - $(window).scrollTop();
                    } else {
                        newHeight = innerBody.offsetHeight + extraSpace;
                    }
                    newHeight = Math.max(newHeight, toolbarList.height());

                    // hide scrollbar
                    innerBody.css({
                        overflow: "hidden",
                        scroll: "no"
                    });

                    $(this.iframe).height(newHeight);
                    $(this.textarea).height(newHeight);
                } else {
                }
            },
            // }}}
            // {{{ updateDepageEditorInput()
            updateDepageEditorInput : function() {
                if (this.wysiwyg) {
                    /* Convert spans to semantics in Mozilla */
                    this.paragraphise();
                    this.cleanSource();
                } else {
                    $(this.input).val($(this.textarea).val());
                }
            },
            // }}}
            // {{{ getSelection()
            getSelection : function() {
                var selection = null;
                var range = null;
                var parentnode = null;
                
                /* IE selections */
                if (this.iframe.contentWindow.document.selection) {
                    selection = this.iframe.contentWindow.document.selection;
                    range = selection.createRange();
                    try {
                        parentnode = $(range.parentElement());
                    }
                    catch (e) {
                        return false;
                    }
                }
                /* Mozilla selections */
                else {
                    try {
                        selection = this.iframe.contentWindow.getSelection();
                    }
                    catch (e) {
                        return false;
                    }
                    range = selection.getRangeAt(0);
                    parentnode = $(range.commonAncestorContainer);
                }
                
                return {
                    range: range,
                    parentnode: parentnode
                }
            },
            // }}}
            // {{{ init()
            init : function(settings) {
                var self = this;
                /* Detects if designMode is available */
                if (typeof(document.designMode) != "string" && document.designMode != "off") return;
                self.locked = true;
                self.cleaning = false;
                self.DOMCache = "";
                self.wysiwyg = true;
                self.createDOM();
                self.writeDocument(); // Fill editor with old textarea content
                self.makeEditable();
                self.modifyFormSubmit();
                self.autogrow();

                $(window).scroll(function() {
                    self.autogrow();
                });
            }                
            // }}}
        });
        this.init();
    };
    /* }}} */
    // {{{ depageEditorToolbar class
    // depageEditorToolbar class
    var depageEditorToolbar = function(editor) {
        $.extend(this, {
            // {{{ createDOM()
            createDOM : function() {
                var self = this;
                /* Create toolbar ul element */
                this.itemsList = document.createElement("ul");
                $(this.itemsList).addClass("depageEditorToolbar");

                /* Create toolbar items */
                $.each(this.depageEditor.settings.toolbarItems, function(i, name) {
                    if(name == "formatblock") self.addSelect(name);
                    else self.addButton(name);
                });
            },
            // }}}
            // {{{ addButton()
            addButton : function(buttonName) {
                var button = $.depageEditorToolbarItems[buttonName];
                var menuItem = $(document.createElement("li"));
                var buttonTitle = (typeof(this.depageEditor.settings.translation[buttonName]) != 'undefined' ) ?
                    this.depageEditor.settings.translation[buttonName] : button.label;
                var link = $(document.createElement("a")).attr({
                    'title' : buttonTitle,
                    'class' : button.className,
                    'href' : '#' + buttonTitle
                }).click( function() {
                    return false;
                });
                button.editor = this.depageEditor;
                $(link).data('action', button);
                $(link).data('editor', this.depageEditor);
                link.bind('click', button.action);
                link.append(document.createTextNode(buttonTitle));
                menuItem.append(link);
                $(this.itemsList).append(menuItem);
            },
            // }}}
            // {{{ addSelect()
            addSelect : function(selectName) {
                var self = this;
                var select= $.depageEditorToolbarItems[selectName];
                var menuItem = $(document.createElement("li")).attr('class', 'depageEditorEditSelect');
                var selectElement = $(document.createElement("select")).attr({
                    'name' : select.name,
                    'class' : select.className
                });
                $(selectElement).data('editor', this.depageEditor);
                $(selectElement).change(select.action);

                var legend = $(document.createElement("option"));
                var selectLabel = (typeof(this.depageEditor.settings.translation[selectName]) != 'undefined' ) ?
                    this.depageEditor.settings.translation[selectName] : select.label;
                legend.append(document.createTextNode(selectLabel));
                selectElement.append(legend);
                
                $.each(this.depageEditor.settings.selectBlockOptions, function(i, value) {        
                    var option = $(document.createElement("option")).attr('value',value);
                    option.append(document.createTextNode(self.depageEditor.settings.translation[value]));
                    selectElement.append(option);
                });

                menuItem.append(selectElement);
                $(this.itemsList).append(menuItem);
            },
            // }}}
            // {{{ disable()
            disable : function() {
                $(this.itemsList).toggleClass("depageEditorSource");
                $(this.itemsList).find('li select').attr('disabled','disabled');
            },
            // }}}
            // {{{ enable()
            enable : function() {
                /* Change class to enable buttons using CSS */
                $(this.itemsList).toggleClass("depageEditorSource");
                $(this.itemsList).find("select").removeAttr("disabled");
            },
            // }}}
            // {{{ checkState()
            checkState : function(depageEditor, resubmit) {
                if (!resubmit) {
                    /* Allow browser to update selection before using the selection */
                    setTimeout(function(){depageEditor.toolbar.checkState(depageEditor, true); return true;}, 200);
                    return true;
                }

                /* Turn off all the buttons */
                $(depageEditor.toolbar.itemsList).find('a').removeClass('on');

                var selection = depageEditor.getSelection();
                var range = selection.range;
                var parentnode = selection.parentnode;

                while (parentnode[0].nodeType == 3) { // textNode
                    parentnode = parentnode.parent();
                }
                while (!parentnode.is('body')) {
                    if (parentnode.is('a')) {
                        depageEditor.toolbar.setState("link", "on");
                    } else if (parentnode.is('em') || parentnode.is('i')) {
                        depageEditor.toolbar.setState("italic", "on");
                    } else if (parentnode.is('strong') || parentnode.is('b')) {
                        depageEditor.toolbar.setState("bold", "on");
                    } else if (parentnode.is('span') || parentnode.is('p')) {
                        if (parentnode.css('font-style') == 'italic') depageEditor.toolbar.setState("italic", "on");
                        if (parentnode.css('font-weight') == 'bold') depageEditor.toolbar.setState("bold", "on");
                    } else if (parentnode.is('ol')) {
                        depageEditor.toolbar.setState("orderedlist", "on");
                        depageEditor.toolbar.setState("unorderedlist", "off");
                    } else if (parentnode.is('ul')) {
                        depageEditor.toolbar.setState("orderedlist", "on");
                        depageEditor.toolbar.setState("unorderedlist", "off");
                    } else {
                        depageEditor.toolbar.setState("formatblock", parentnode[0].nodeName.toLowerCase());
                    }
                    parentnode = parentnode.parent();
                }                        
            },
            // }}}
            // {{{ setState()
            setState: function(state, status) {
                if (state != "SelectBlock") {
                    $(this.itemsList).find('.' + $.depageEditorToolbarItems[state].className).addClass('on');
                } else {
                    $(this.itemsList).find('.' + $.depageEditorToolbarItems[state].className).val(status);            
                }
            },
            // }}}
            // {{{ init()
            init : function(editor) {
                this.depageEditor = editor;
                this.createDOM();
            }
            // }}}
        });
        this.init(editor);
    };
    // }}}
    // {{{ depageEditorToolbarItems class
    /* depageEditorToolbarItems class, can be extended using $.extend($.depageEditorToolbarItems, { (...) } */
    var depageEditorToolbarItems = function() {
        /* Defines singleton logic */
        depageEditorToolbarItemsClass = this.constructor;
        if(typeof(depageEditorToolbarItemsClass.singleton) != 'undefined') {
            return depageEditorToolbarItemsClass.singleton;
        } else { 
            depageEditorToolbarItemsClass.singleton = this;
        }

        /* Extends class with items properties, will only be executed once */
        $.extend(depageEditorToolbarItemsClass.singleton, {
            // {{{ bold()
            bold : {
                className : 'depageEditorButtonBold',
                action : function(){
                    var editor = $.data(this, 'editor');
                    if(!editor.wysiwyg) {
                        return;
                    }
                    editor.styleWithCSS();
                    editor.iframe.contentWindow.document.execCommand('bold', false, null);
                    editor.toolbar.setState('bold', "on");
                    editor.focusEditor();
                }
            },
            // }}}
            // {{{ italic()
            italic : {
                className : 'depageEditorButtonItalic',
                action : function(){
                    var editor = $.data(this, 'editor');
                    if(!editor.wysiwyg) return;
                    editor.styleWithCSS();
                    editor.iframe.contentWindow.document.execCommand('italic', false, null);
                    editor.toolbar.setState('italic', "on");
                    editor.focusEditor();
                }
            },
            // }}}
            // {{{ link()
            link : {
                className : 'depageEditorButtonHyperlink',
                action : function(){
                    var editor = $.data(this, 'editor');
                    if(!editor.wysiwyg) return;
                    if ($(this).hasClass("on"))  {
                        editor.styleWithCSS();
                        editor.iframe.contentWindow.document.execCommand("Unlink", false, null);
                        editor.focusEditor();
                        return;
                    }
                    var selection = $(editor.iframe).getSelection();
                    if (selection == "") {
                        alert(editor.settings.translation.selectTextToHyperlink);
                        editor.focusEditor();
                        return;
                    }
                    var url = prompt(editor.settings.translation.linkURL, "http://");
                    if (url != null) {            
                        editor.styleWithCSS();
                        editor.iframe.contentWindow.document.execCommand("CreateLink", false, url);
                        editor.toolbar.setState('link', "on");
                        editor.focusEditor();
                    }
                }
            },
            // }}}
            // {{{ orderedList()
            orderedlist : {
                className : 'depageEditorButtonOrderedList',
                action : function(){
                    var editor = $.data(this, 'editor');
                    if(!editor.wysiwyg) return;
                    editor.styleWithCSS();
                    editor.iframe.contentWindow.document.execCommand('insertorderedlist', false, null);
                    editor.toolbar.setState('orderedlist', "on");
                    editor.focusEditor();
                }
            },
            // }}}
            // {{{ unorderedList()
            unorderedlist : {
                className : 'depageEditorButtonUnorderedList',
                action : function(){
                    var editor = $.data(this, 'editor');
                    if(!editor.wysiwyg) return;
                    editor.styleWithCSS();
                    editor.iframe.contentWindow.document.execCommand('insertunorderedlist', false, null);
                    editor.toolbar.setState('unorderedlist', "on");
                    editor.focusEditor();
                }
            },
            // }}}
            // {{{ image()
            image : {
                className : 'depageEditorButtonImage',
                action : function(){
                    var editor = $.data(this, 'editor');
                    if(!editor.wysiwyg) return;
                    var imgLoc = prompt(editor.settings.translation.imageLocation, "");
                    if (imgLoc != null && imgLoc != "") {
                        var alt = prompt(editor.settings.translation.imageAlternateText, "");
                        alt = alt.replace(/"/g, "'");
                        $(editor.iframe).appendToSelection('img', {
                            'src' : imgLoc,
                            'alt' : alt
                        }, null, true);
                    }
                    editor.focusEditor();
                }
            },
            // }}}
            // {{{ htmlsource()
            htmlsource : {
                className : 'depageEditorButtonHTML',
                action : function() {
                    var editor = $.data(this, 'editor');
                    editor.switchMode();
                }
            },
            // }}}
            // {{{ formatBlock()
            formatblock : {
                className : 'depageEditorSelectformatblock',
                action : function(){
                    // @todo test if this is inside a class
                    var editor = $.data(this, 'editor');
                    if(!editor.wysiwyg) return;
                    var tag = $.browser.msie ? "<" + $(this).val() + ">" : $(this).val();
                    editor.styleWithCSS();
                    editor.iframe.contentWindow.document.execCommand('FormatBlock', false, tag);
                    editor.focusEditor();
                }
            }
            // }}}
        });
    };
    // }}}

    $.depageEditorToolbarItems = new depageEditorToolbarItems();

    // {{{ jquery.fn extensions 
    $.fn.extend({
        // {{{ getSelection()
        getSelection : function() {
            if(!this.is('iframe')) return;
            else iframe = this[0];
            return (iframe.contentWindow.document.selection) ?
                iframe.contentWindow.document.selection.createRange().text :
                iframe.contentWindow.getSelection().toString();
        },
        // }}}
        // {{{ appendToSelection()
        appendToSelection : function(nodeType, attr, contentText, singleTag) {
            if(!this.is('iframe')) return;
            else iframe = this[0];
            var selection, range;
            if($.browser.msie) {
                var html;
                html = '<' + nodeType;
                $.each(attr, function(label, value) { html += ' ' + label + '="' + value + '"' });
                if(singleTag) html += ' />';
                else {
                    html += '>';
                    if(contentText && typeof(contentText) != 'undefined') html += contentText;
                    html += '</' + nodeType + '>';
                }
                selection = iframe.contentWindow.document.selection;
                range = selection.createRange();
                if($(range.parentElement()).parents('body').is('#iframeBody ')) return;
                range.collapse(false);
                range.pasteHTML(html);
            }
            else {
                selection = iframe.contentWindow.getSelection();
                range = selection.getRangeAt(0);
                range.collapse(false);
                var element = iframe.contentWindow.document.createElement(nodeType);
                $(element).attr(attr);                        
                if(contentText && typeof(contentText) != 'undefined') $(element).append(document.createTextNode(contentText));
                range.insertNode(element);
            }
        },
        // }}}
        // {{{ depageEditor()
        depageEditor : function(settings) {
            var defaultTranslation = {
                bold : 'Bold',
                italic : 'Italic',
                link : 'Hyperlink',
                unorderedlist : 'Unordered List',
                orderedlist : 'Ordered List',
                image : 'Insert image',
                htmlsource : 'HTML Source',
                formatblock : 'Change Block Type',
                h1 : "Heading 1",
                h2 : "Heading 2",
                h3 :"Heading 3",
                h4 : "Heading 4",
                h5 : "Heading 5",
                h6 : "Heading 6",
                p : "Paragraph",
                selectTextToHyperlink : "Please select the text you wish to hyperlink.",
                linkURL : "Enter the URL for this link:",
                imageLocation : "Enter the location for this image:",
                imageAlternateText : "Enter the alternate text for this image:"
            };
            
            /* settings for content pasted from a web page */
            var defaultUndesiredTags = {
                'script' : 'remove',
                'meta' : 'remove',
                'link' : 'remove',
                'basefont' : 'remove',
                'object' : 'remove',
                'applet' : 'remove',
                'input' : 'remove',
                'select': 'remove',
                'textarea' : 'remove',
                'button' : 'remove',
                'isindex' : 'remove',
                'area' : 'remove',
                'frame' : 'remove',
                'frameset' : 'remove',
                'noframes' : 'remove',
                'iframe' : 'remove'
                // there sure is some more elements to be added to the list
            };

            var defaultAllowedTags = [
                'p',
                'h1',
                'h2',
                'ul',
                'ol',
                'li',

                'a',
                'b',
                'strong',
                'i',
                'em'
            ];

            var defaultAllowedAttributes = [
                'class',
                'id',
                'href',
                'title',
                'alt',
                'src'
            ];

            settings = $.extend({
                insertParagraphs : true,
                stylesheet : 'depageEditorContent.css',
                toolbarItems : ['bold','italic','link','orderedlist','unorderedlist','htmlsource','formatblock'],
                selectBlockOptions : ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p'],
                undesiredTags : defaultUndesiredTags,
                allowedTags : defaultAllowedTags,
                allowedClasses : new Array(),
                allowedIDs : new Array(),
                allowedAttributes : defaultAllowedAttributes,
                containerClass : 'depageEditor',
                translation : defaultTranslation,
                autogrow : false
            }, settings);

            // remove blocktypes that are not in allowedTags
            for (var i = settings.selectBlockOptions.length - 1; i >= 0; i--) {
                if ($.inArray(settings.selectBlockOptions[i], settings.allowedTags) === -1) {
                    settings.selectBlockOptions.splice(i, 1);
                }
            }
            
            settings.undesiredTags = (settings.undesiredTags.length != defaultUndesiredTags.length) ?
                $.removeDuplicate($.merge(settings.undesiredTags, defaultUndesiredTags)) : settings.undesiredTags;

            settings.allowedAttributes = (settings.allowedAttributes.length != defaultAllowedAttributes.length) ?
                $.removeDuplicate($.merge(settings.allowedAttributes, defaultAllowedAttributes)) : settings.allowedAttributes;
            
            return this.each(function(){
                new depageEditor(this, settings);
            });
        }
        // }}}
    });
    // }}}
})(jQuery);

/* vim:set ft=javascript sw=4 sts=4 fdm=marker : */
