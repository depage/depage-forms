/*
 * @require framework/shared/jquery-1.12.3.min.js
 * @require framework/HtmlForm/lib/js/jquery.tools.js
 * @require framework/HtmlForm/lib/js/lodash.custom.min.js
 * @require framework/HtmlForm/lib/js/selectize.js
 * @require framework/HtmlForm/lib/js/squire-raw.js
 * @require framework/HtmlForm/lib/js/purify.js
 */

;(function($){
    // {{{ $.fn.scrollParent
    $.fn.scrollParent = function() {
        var position = this.css( "position" ),
        excludeStaticParent = position === "absolute",
        scrollParent = this.parents().filter( function() {
            var parent = $( this );
            if ( excludeStaticParent && parent.css( "position" ) === "static" ) {
                return false;
            }
            return (/(auto|scroll)/).test( parent.css( "overflow" ) + parent.css( "overflow-y" ) + parent.css( "overflow-x" ) );
        }).eq( 0 );

        return position === "fixed" || !scrollParent.length ? $( this[ 0 ].ownerDocument || document ) : scrollParent;
    };
    // }}}
    // {{{ IE foreach
    if (typeof NodeList !== "undefined" && NodeList.prototype && !NodeList.prototype.forEach) {
        // Yes, there's really no need for `Object.defineProperty` here
        NodeList.prototype.forEach = Array.prototype.forEach;
    }
    // }}}

    // {{{ add validation-"effects"
    $.tools.validator.addEffect('depageEffect', function(errors, event) {
        // "show" function
        $.each(errors, function(index, error) {
            // erroneous input paragraph
            var $input = $(error.input);
            var errorParagraph = $input.parents('p');

            // if there's no error message
            var $errorMessage = errorParagraph.find('.errorMessage');
            if ($errorMessage.length === 0) {
                // add error notices
                errorParagraph.append('<span class="errorMessage">' + errorParagraph.attr('data-errorMessage')+ '</span>');
                errorParagraph.addClass('error');
            } else {
                $errorMessage.show();
            }
        });

    // remove error notices when inputs turn valid
    }, function(inputs) {
        $.each(inputs, function(index, input) {
            var inputParagraph = $(input).parents('p');
            inputParagraph.removeClass('error');
            inputParagraph.find('.errorMessage').remove();
        });
    });
    // }}}

    // {{{ ie
    var ie = (function(){
        var undef,
            v = 3,
            div = document.createElement('div'),
            all = div.getElementsByTagName('i');

        while (
            div.innerHTML = '<!--[if gt IE ' + (++v) + ']><i></i><![endif]-->',
            all[0]
        );

        return v > 4 ? v : undef;

    }());
    // }}}
    // {{{ splitTag
    var splitTag = function(tag) {
        var result = /([a-zA-Z0-9]*)(\.(.*))?/.exec(tag);

        var attr = [];

        if (typeof result[3] != 'undefined') {
            attr['class'] = result[3];
        }

        return {
            tag: result[1],
            className: result[3],
            attributes: attr
        };
    };
    // }}}

    // {{{ setupForm()
    function setupForm(form) {
        var $form = $(form);
        var check = $form.attr('data-jsvalidation');
        var autosave = $form.attr('data-jsautosave');

        if ($form.data("depage-form-initialized")) return;

        // {{{ hide errormessages on click
        $form.on('focus', '.error input', function(event) {
            $(this).parents('.error').find('.errorMessage').hide();
        });
        // }}}
        // {{{ focus input on click on error message
        $form.on('click', '.errorMessage', function(event) {
            var $input = $(this).parents('.error').find('input, select, textarea').focus();
            var e;

            try {
                // try to open select on click
                e = document.createEvent('MouseEvents');
                e.initMouseEvent("mousedown", true, true, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
                $input[0].dispatchEvent(e);
            } catch (exception) {
            }
        });
        // }}}

        // {{{ add tap-events on labels for chackboxes and radiobuttons on iPhone/iPad
        $('input:checkbox, input:radio', form).each(function() {
            var $label = $(this).parents("label");

            $label.on('click', function(e) {
                // this seems to be enough to fire the default click event on iOS -> no additional action necessary
            });
        });
        // }}}
        // {{{ add handlers to textarea
        $('.input-textarea', form).each(function() {
            var options = $.parseJSON($(this).attr('data-textarea-options'));
            var $textarea = $("textarea", this);
            var $textareaSize = $("<textarea class=\"textarea-size\"></textarea>").appendTo($textarea.parent()).height("auto").css({
                position: "absolute",
                left: "-10000px"
            });

            if (options.autogrow && !(ie && is < 9) ) {
                $textarea.autogrow = function() {
                    $textareaSize[0].value = $textarea[0].value;

                    // set new height
                    var newHeight = $textareaSize[0].scrollHeight + 30;
                    $textarea.height(newHeight);
                };
                $textarea.on('mouseup', function() {
                }).on('keyup', function() {
                    $textarea.autogrow();
                }).on('keydown', function() {
                    $textarea.autogrow();
                }).on('scroll', function() {
                    $textarea.autogrow();
                }).autogrow();
            }
        });
        // }}}

        // {{{ focus first input with autofocus
        $('input[autofocus]:first', form).focus();
        // }}}

        // {{{ add missing "http://" to url inputs
        $("input[type='url']", form).change( function() {
            if (this.value !== "" && !this.value.match(/[a-z][\w\-]+:\/\/.*/)) {
                this.value = "http://" + this.value;
            }
        });
        // }}}

        if ($('.input-richtext', form).length > 0) {
            if (typeof Squire !== 'undefined') {
                $('.input-richtext', form).each(function() {
                    setupRichtextEditor(this);
                });
            } else {
                console.log("Squire.js not included but needed for richtext support");
            }
        }

        if (typeof $.fn.selectize !== 'undefined') {
            setupSelectize($form);
        }

        // {{{ validate
        // validate on blur or change
        if ((check == 'blur') || (check == 'change')) {
            // @todo add also event for "submit" nonetheless
            $form.validator({
                effect: 'depageEffect',
                inputEvent: check,
                errorInputEvent: check
            });
        // validate on submit
        } else if (check == 'submit') {
            $form.validator({
                effect: 'depageEffect',
                // do not validate inputs when they are edited
                errorInputEvent: null
            });
        }
        $form.find("p.cancel input[type='submit'], p.back input[type='submit']").on("click", function() {
            // disable browser validation before submitting
            $form.attr('novalidate', 'novalidate');

            // disable custom js validator
            $form.validator({
                formEvent: null
            });

            return true;
        });
        // }}}
        // {{{ add inFail and onSuccess handlers
        $form.on("fail", function(e, errors) {
            // error found
            $form.find(".submit").addClass("error");
        });
        $form.on("success", function(e, errors) {
            // count error messages, because onSuccess is called for a successful validated input
            var errNum = $form.find(".errorMessage").length;

            if (errNum > 0) {
                $form.find(".submit").addClass("error");
            } else {
                $form.find(".submit").removeClass("error");
            }
        });
        // }}}
        // {{{ add focus class to fieldset
        $("input, textarea, select, .input-richtext .textarea-content")
            .on("focus", function() {
                $(this).parents("fieldset").addClass("focus");
            })
            .on("blur", function() {
                $(this).parents("fieldset").removeClass("focus");
            });
        // }}}

        // {{{ autosave
        if (autosave == "true") {
            var saveInterval = 1000;
            var now = new Date();

            form.data = form.data || {};
            form.data.saving = false;
            form.data.lastsave = now.getTime();
            form.data.timer = null;

            form.data.getFormValues = function() {
                var data = {};

                $("input, select, textarea", form)
                    .filter(function() {
                        return !$(this).parent().hasClass("cancel");
                    })
                    .each( function () {
                        var type = $(this).attr("type");
                        if ((type == "radio")) {
                            if (this.checked) {
                                data[this.name] = this.value;
                            }
                        } else if (type == "checkbox" && $(this).parents("p").hasClass("input-boolean")) {
                            if (this.checked) {
                                data[this.name] = this.value;
                            }
                        } else if (type == "checkbox") {
                            if (this.checked) {
                                data[this.name] = data[this.name] || [];
                                data[this.name].push(this.value);
                            }
                        } else {
                            data[this.name] = $(this).val();
                        }
                    });
                data.formAutosave = "true";

                return data;
            };
            form.data.lastSaved = form.data.getFormValues();
            form.data.autosave = function() {
                // trigger event before autosaving
                var event = jQuery.Event("depageForm.beforeAutosave");
                $form.trigger(event);
                if (event.isDefaultPrevented()) return;

                var data = form.data.getFormValues();

                if (JSON.stringify(data) === JSON.stringify(form.data.lastSaved)) {
                    // data is the same -> skip save
                    return;
                }
                form.data.saving = true;

                $.post(form.action, data, function(response, textStatus) {
                    now = new Date();

                    form.data.lastsave = now.getTime();
                    form.data.saving = false;
                    form.data.lastSaved = data;

                    //@todo trigger different event when autosave was unsuccessful?
                    $form.triggerHandler("depageForm.autosaved");
                });
            };
            form.data.changed = function(saveImmediately) {
                now = new Date();
                clearTimeout(form.data.timer);

                if (!form.data.saving) {
                    if (saveImmediately || now.getTime() - form.data.lastsave > saveInterval) {
                        form.data.autosave();
                    } else {
                        form.data.timer = setTimeout(function() {
                            form.data.changed();
                        }, saveInterval);
                    }
                }
            };

            // normal inputs
            $("input, select, textarea", form).on("change", function() {
                form.data.changed(this.type != "hidden");
            });
            // only for textarea
            $("input, textarea", form).on("keyup", function() {
                form.data.changed();
            });
        }
        // }}}

        $form.data("depage-form-initialized", true);
    }
    // }}}
    // {{{ setupRichtextEditor()
    function setupRichtextEditor(container) {
        // @todo merge option arrays
        var options = {};
        var $container = $(container);
        $.extend(options, $.parseJSON($container.attr('data-textarea-options')), $.parseJSON($container.attr('data-richtext-options')));

        var $textarea = $("textarea", container);
        var maxlength = $textarea.attr("maxlength") || -1;
        var classes = $textarea.attr("class");
        var $div = $("<div class=\"textarea richtext\"><div class=\"textarea-content\"></div></div>")
            .insertAfter($textarea)
            .children(".textarea-content");
        var $input = $("<input />").attr({
            type : 'hidden',
            name : $textarea.attr('name'),
            value : $textarea[0].value // old textarea value
        }).insertAfter($textarea);

        var allowedTags = [];

        for (var i = 0; i < options.allowedTags.length; i++) {
            allowedTags.push(splitTag(options.allowedTags[i]).tag);
        }

        var editor = new Squire($div[0], {
            blockTag: 'P',
            // @todo check why this throws an error
            sanitizeToDOMFragment: function(html, isPaste, self) {
                var frag = DOMPurify.sanitize(html, {
                    ALLOWED_TAGS: allowedTags,
                    ALLOWED_ATTR: ['class', 'href', 'target', 'alt', 'title'],
                    WHOLE_DOCUMENT: false,
                    RETURN_DOM: true,
                    RETURN_DOM_FRAGMENT: true,
                    ALLOWED_URI_REGEXP: /^(?:(?:(?:f|ht)tps?|mailto|tel|callto|cid|xmpp|pageref|libref|footnote):|[^a-z]|[a-z+.\-]+(?:[^a-z+.\-:]|$))/i
                });
                // remove br's that are the last children in a node
                var brs = frag.querySelectorAll('br');
                brs.forEach(function(br) {
                    if (br.nextSibling === null) {
                        br.parentNode.removeChild(br);
                    }
                });

                return frag;
            }
        });
        $div.data("editor", editor);
        editor.setHTML($textarea[0].value);

        $textarea.remove();

        var toolbar = new Toolbar(container, options, editor);
        var scrollTop = 0;

        $(editor)
            .on("input", _.throttle(function() {
                $input[0].value = editor.getHTML();
                $input.trigger("change");

                toolbar.hide();
            }, 1000, {
                leading: true,
                trailing: true
            }))
            .on("focus", function() {
                $div.parent()
                    .addClass("focus")
                    .scrollTop(scrollTop);
            })
            .on("blur", function() {
                $input[0].value = editor.getHTML();
                $input.trigger("change");

                $div.parent().removeClass("focus");

                toolbar.hide();
            })
            .on("willPaste", function(e, frag) {
                console.log(e.originalEvent.fragment);
            })
            .on("select", function() {
                if (editor._isFocused) {
                    toolbar.show().setPositionBySelection();
                }
            })
            .on("keypress", function(e) {
                // @todo fix table selection and navigation on safari
                if (e.key != "Tab" && e.which != 94) return;

                var range = editor.getSelection();
                Squire.moveRangeBoundariesDownTree(range);

                cell = range.startContainer;
                while (cell && !Squire.isTable(cell)) {
                    cell = cell.parentNode;
                }

                if (!cell || cell.nodeName != 'TD') return;

                var $cell = $(cell);
                var $cells = $cell.parents("table").find("td");
                var cellIndex = $cells.index($cell);

                if (e.shiftKey) {
                    // backwards
                    cellIndex--;
                } else {
                    // forward
                    cellIndex++;
                }

                if (cellIndex < 0 || cellIndex >= $cells.length) return;

                var r = document.createRange();
                if ($cells[cellIndex].firstChild == null) {
                    $cells[cellIndex].appendChild(document.createTextNode(""));
                }
                r.setStartBefore($cells[cellIndex].firstChild);
                r.setEndAfter($cells[cellIndex]);

                editor.setSelection(r);
                e.stopPropagation();

                return false;
            });

        $div
            .on("dblclick", "a", function() {
                var $a = $(this);

                setTimeout(function() {
                    var r = document.createRange();
                    r.selectNode($a[0]);
                    editor.setSelection(r);
                    editor.showLinkDialog($a.attr("href"), function(value) {
                        editor.changeFormat(null, { tag: "a" });
                        if (value != "") {
                            editor.changeFormat({ tag: "a", attributes: { href: value }});
                        }
                        editor.focus();
                    });
                }, 50);
            })
            .on("click", "table td:last-child", editor.handleTableButtonClick);

        $div.parent().on("scroll", function() {
            scrollTop = $(this).scrollTop();
        });

        $div.data("toolbar", toolbar);

        if (maxlength > 0) {
            var isMac = /Mac OS X/.test(navigator.userAgent);

            $(editor).on("keydown", function(e) {
                var keycode = e.keyCode;

                //List of keycodes of printable characters from:
                //http://stackoverflow.com/questions/12467240/determine-if-javascript-e-keycode-is-a-printable-non-control-character
                var printable =
                    (keycode > 47 && keycode < 58)   || // number keys
                    keycode == 32 || keycode == 13   || // spacebar & return key(s) (if you want to allow carriage returns)
                    (keycode > 64 && keycode < 91)   || // letter keys
                    (keycode > 95 && keycode < 112)  || // numpad keys
                    (keycode > 185 && keycode < 193) || // ;=,-./` (in order)
                    (keycode > 218 && keycode < 223);   // [\]' (in order)

                // @todo check if text is selected because it would be overridden
                var shortcutKey = (isMac && e.metaKey) || e.ctrlKey;
                if (!shortcutKey && printable) {
                    return $div.text().length < maxlength;
                }
            });
        }
    }
    // }}}
    // {{{ setupSelectize()
    function setupSelectize($form) {
        $form.find('select').each(function() {
            var $select = $(this);
            var maxItems = $select.data('max-items');

            $select.selectize({
                maxItems: maxItems,
                plugins: ['remove_button'],
                persist: false
            });
        });
    }
    // }}}

    if (typeof Squire !== 'undefined') {
        // {{{ Squire.replaceBlock
        Squire.prototype.replaceBlock = function(tag) {
            var result = splitTag(tag);

            this.modifyBlocks(function(frag) {
                var newFrag = frag.ownerDocument.createDocumentFragment();
                var i, l;

                for ( i = 0, l = frag.children.length; i < l; i += 1 ) {
                    var el = frag.ownerDocument.createElement(result.tag);
                    while (frag.children[i].childNodes.length > 0) {
                        var node = frag.children[i].childNodes[0];
                        if (node.nodeName == "LI") {
                            // if element is a list add it's children instead
                            while (node.childNodes.length > 0) {
                                el.appendChild(node.childNodes[0]);
                            }
                            node.parentNode.removeChild(node);
                        } else {
                            el.appendChild(node);
                        }
                    }
                    newFrag.appendChild(el);
                }

                if (typeof result.className != 'undefined') {
                    for (i = 0; i < newFrag.children.length; i++) {
                        newFrag.children[i].className = result.className;
                    }
                }

                return newFrag;
            }, this.getSelection());

            return this.focus();
        };
        // }}}
        // {{{ Squire.makeHeadline
        Squire.prototype.makeHeadline = function(level) {
            if (typeof level == 'undefined') {
                level = 1;
            }
            return this.replaceBlock("h" + level);
        };
        // }}}
        // {{{ Squire.removeHeadline
        Squire.prototype.removeHeadline = function() {
            return this.replaceBlock("p");
        };
        // }}}
        // {{{ Squire.getPosBySelection()
        Squire.prototype.getPosBySelection = function(offsetTop, offsetLeft) {
            var range = this.getSelection();
            var rects = range.getClientRects();
            var scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
            var scrollLeft = document.documentElement.scrollLeft || document.body.scrollLeft;

            offsetTop = offsetTop || 0;
            offsetLeft = offsetLeft || 0;

            // @todo check if mouse position is inside rect and position the toolbar next to mouse
            // @todo check that toolbar stays in visible area of window
            // @todo change position only if necessary (if selection has changed)
            // @todo adjust toolbar position on iOS because of native popup

            var left = 1000000000;
            var right = 0;
            var top = 0;
            for (var i = 0; i < rects.length; i++) {
                left = Math.min(left, rects[i].left);
                right = Math.max(right, rects[i].right);
            }
            top = rects[0].top + scrollTop - offsetTop;
            left = left + (right - left) * 0.5 + scrollLeft - offsetLeft;

            if (top < 60) {
                top = 60;
            }

            return {
                top: top,
                left: left,
            };
        };
        // }}}
        // {{{ Squire.showLinkDialog()
        Squire.prototype.showLinkDialog = function(href, callback) {
            // @todo implement fallback -> alert?
        };
        // }}}
        // {{{ Squire.handleTableButtonClick()
        Squire.prototype.handleTableButtonClick = function(e) {
            var $cell = $(e.currentTarget);

            if (e.offsetX < $cell.width()) return;

            var $currentRow = $cell.parent();

            if (e.offsetY < 20) {
                var $newRow = $currentRow.clone();

                $newRow.children("td").text("").append("<p><br /></p>");
                $newRow.insertAfter($currentRow);
            } else if (e.offsetY < 40 && $currentRow.parent().children("tr").length > 1) {
                $currentRow.remove();
            }

            return false;
        };
        // }}}
    }

    // {{{ Toolbar constructor
    var Toolbar = function(container, options, editor) {
        this.container = container;
        this.eventNamespace = "depageRtfToolbar" + $(this.container).index();
        this.editor = editor;
        this.options = options;
        this.visible = false;
        this.hideTimeout = null;
        this.mouseX = null;

        var toolbar = this;

        this.$toolbar = $("<ul class=\"depageEditorToolbar\"></ul>");
        this.$toolbar.on("click", function(e) {
            e.stopPropagation();
        });
        $(this.container)
            .on("click", function(e) {
                e.stopPropagation();
            })
            .on("mousemove", function(e) {
                toolbar.mouseX = e.offsetX;
            });

        this.tagToFunction = {
            'a': {
                add: function() {
                    editor.showLinkDialog("", function(value) {
                        editor.changeFormat(null, { tag: "a" });
                        if (value != "") {
                            editor.changeFormat({ tag: "a", attributes: { href: value }});
                        }
                        editor.focus();
                    });
                },
                remove: function() { editor.changeFormat(null, { tag: "a" }); }
            },
            'b': "inline",
            'i': "inline",
            'u': "inline",
            's': "inline",
            'small': "inline",
            'sub': "inline",
            'sup': "inline",
            'div': "block",
            'p': "block",
            'h1': "block",
            'h2': "block",
            'h3': "block",
            'h4': "block",
            'h5': "block",
            'h6': "block",
            'ul': {
                add: function() { editor.makeUnorderedList(); },
                remove: function() { editor.removeList(); }
            },
            'ol': {
                add: function() { editor.makeOrderedList(); },
                remove: function() { editor.removeList(); }
            }
        };

        var buttonTags = options.allowedTags.slice();
        var hasOtherBlocks = buttonTags.indexOf("ul") !== -1 ||
            buttonTags.indexOf("ol") !== -1 ||
            buttonTags.indexOf("h1") !== -1 ||
            buttonTags.indexOf("h2") !== -1 ||
            buttonTags.indexOf("h3") !== -1 ||
            buttonTags.indexOf("h4") !== -1 ||
            buttonTags.indexOf("h5") !== -1 ||
            buttonTags.indexOf("h6") !== -1;

        var pindex = buttonTags.indexOf("p");
        if (!hasOtherBlocks && pindex !== -1) {
            buttonTags.splice(pindex, 1);
        }

        for (var i = 0; i < buttonTags.length; i++) {
            this.addButton(buttonTags[i]);
        }
    };
    // }}}
    // {{{ Toolbar.addButton
    Toolbar.prototype.addButton = function(newtag) {
        var result = splitTag(newtag);

        if (typeof this.tagToFunction[result.tag] !== 'undefined') {
            var className = newtag.replace(".", "-");
            var title = result.tag;
            if (title == "small") {
                title = "s";
            }
            var $button = $("<li class=\"format-" + className + "\" title=\"" + newtag + "\">" + title + "</li>");
            var editor = this.editor;
            var tagToFunction = this.tagToFunction;

            $button
                .appendTo(this.$toolbar)
                .on("click", function() {
                    if (tagToFunction[result.tag] == "inline") {
                        if (editor.hasFormat(newtag)) {
                            editor.changeFormat(null, { tag: newtag });
                        } else {
                            editor.changeFormat({ tag: newtag });
                        }
                    } else if (tagToFunction[result.tag] == "block") {
                        if (editor.hasFormat(result.tag, result.attributes)) {
                            editor.replaceBlock("p");
                        } else {
                            editor.replaceBlock(newtag);
                        }
                    } else {
                        if (editor.hasFormat(newtag)) {
                            tagToFunction[newtag].remove();
                        } else {
                            tagToFunction[newtag].add();
                        }
                    }
                    editor.focus();
                });
        }

        return this;
    };
    // }}}
    // {{{ Toolbar.show
    Toolbar.prototype.show = function() {
        if (this.$toolbar.children("li").length == 0) return;

        this.$toolbar.appendTo("body");

        this.visible = true;

        var toolbar = this;

        for (var i = 0; i < this.options.allowedTags.length; i++) {
            var tag = this.options.allowedTags[i];
            var className = tag.replace(".", "-");
            var result = splitTag(tag);

            this.$toolbar.children(".format-" + className).toggleClass("active", this.editor.hasFormat(result.tag, result.attributes));
        }

        $("body")
            .off("click." + this.eventNamespace)
            .on("click." + this.eventNamespace, function() {
                toolbar.hide();
            });
        $(toolbar.container).scrollParent()
            .off("scroll." + this.eventNamespace)
            .on("scroll." + this.eventNamespace, function() {
                toolbar.hide(true);
            });

        return this;
    };
    // }}}
    // {{{ Toolbar.hide
    Toolbar.prototype.hide = function(immediately) {
        var toolbar = this;
        var wait = immediately === true ? 0 : 200;

        this.visible = false;

        clearTimeout(toolbar.hideTimeout);
        toolbar.hideTimeout = setTimeout(function() {
            if (!toolbar.visible) {
                toolbar.$toolbar.detach();

                $("body").off("click." + this.eventNamespace);
                $(toolbar.container).scrollParent().off("scroll." + this.eventNamespace);
            }
        }, wait);

        return this;
    };
    // }}}
    // {{{ Toolbar.setPositionBySelection
    Toolbar.prototype.setPositionBySelection = function() {
        this.$toolbar.offset(this.editor.getPosBySelection(this.$toolbar.height() * 1.5, this.$toolbar.width() * 0.5));

        return this;
    };
    // }}}

    /* {{{ jQuery.fn.depageForm() */
    $.fn.depageForm = function(){
        return this.each(function(){
            setupForm(this);
        });
    };
    /* }}} */

    $(document).ready(function () {
        $('.depage-form').depageForm();

        $("body").on("depage.magaziner.loaded", function(e, url, $page) {
            $page.find('.depage-form').depageForm();
        });
    });
})(jQuery);

/* vim:set ft=javascript sw=4 sts=4 fdm=marker : */
