/*
 * @require framework/shared/jquery-1.8.3.js
 * @require framework/HtmlForm/lib/js/jquery.tools.js
 * @require framework/HtmlForm/lib/js/depage-richtext.js
 */

;(function($){
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

    // {{{ setupForm()
    function setupForm(form) {
        var $form = $(form);
        var check = $form.attr('data-jsvalidation');
        var autosave = $form.attr('data-jsautosave');

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

        if (typeof Squire !== 'undefined') {
            $('.input-richtext', form).each(function() {
                setupRichtextEditor(this);
            });
        } else if ($('.input-richtext', form).length > 0) {
            console.log("Squire.js not included but needed for richtext support");
        }

        // {{{ autosave
        if (autosave == "true") {
            var saveInterval = 1000;
            var now = new Date();

            form.data = form.data || {};
            form.data.saving = false;
            form.data.lastsave = now.getTime();
            form.data.timer = null;

            form.data.autosave = function() {
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
                        } else if (type == "checkbox") {
                            if (this.checked) {
                                data[this.name] = data[this.name] || [];
                                data[this.name].push(this.value);
                            }
                        } else {
                            data[this.name] =  $(this).val();
                        }
                    });

                data.formAutosave = "true";
                form.data.saving = true;

                // @todo trigger event before saving
                $.post(form.action, data, function(response, textStatus) {
                    now = new Date();

                    form.data.lastsave = now.getTime();
                    form.data.saving = false;
                    //@todo trigger events whether the autosave was successful or not
                });
            };
            form.data.changed = function(saveImmediately) {
                now = new Date();
                clearTimeout(form.data.timer);

                if (!form.data.saving) {
                    if (saveImmediately || now.getTime() - form.data.lastsave > saveInterval) {
                        form.data.autosave();
                    } else {
                        setTimeout(function() {
                            form.data.changed();
                        }, saveInterval);
                    }
                }
            };

            $("input, select, textarea", form).change( function() {
                form.data.changed(true);
            });
            // only for textarea and richtext
            $("textarea", form).keyup( function() {
                form.data.changed();
            });
        }
        // }}}

    }
    // }}}
    // {{{ setupRichtextEditor()
    function setupRichtextEditor(container) {
        // @todo merge option arrays
        var options = {};
        var $container = $(container);
        $.extend(options, $.parseJSON($container.attr('data-textarea-options')), $.parseJSON($container.attr('data-richtext-options')));

        var $textarea = $("textarea", container);

        var $div = $("<div class=\"textarea richtext\"></div>").insertAfter($textarea);
        var $input = $("<input />").attr({
            type : 'hidden',
            name : $textarea.attr('name'),
            value : $textarea[0].value // old textarea value
        }).insertAfter($textarea);

        var editor = new Squire($div[0], {
            blockTag: 'P',
            sanitizeToDOMFragment: function(html, isPaste, self) {
                return DOMPurify.sanitize(html, {
                    ALLOWED_TAGS: options.allowedTags,
                    WHOLE_DOCUMENT: false,
                    RETURN_DOM: true,
                    RETURN_DOM_FRAGMENT: true
                });
            }
        });
        editor.setHTML($textarea[0].value);

        $textarea.remove();

        var toolbar = new Toolbar(container, options, editor);

        $(editor).on("input", _.throttle(function() {
            $input[0].value = editor.getHTML();

            toolbar.hide();
        }, 100));
        $(editor).on("focus", function() {
        });
        $(editor).on("blur", function() {
            toolbar.hide();
        });
        $(editor).on("select", function() {
            if (editor._isFocused) {
                toolbar.show().setPositionBySelection();
            }
        });
    }
    // }}}

    // {{{ Squire.replaceBlock
    Squire.prototype.replaceBlock = function(tag) {
        this.modifyBlocks(function(frag) {
            var newFrag = frag.ownerDocument.createDocumentFragment();
            var i, l;

            for ( i = 0, l = frag.children.length; i < l; i += 1 ) {
                var el = frag.ownerDocument.createElement(tag);
                while (frag.children[i].childNodes.length > 0) {
                    el.appendChild(frag.children[i].childNodes[0]);
                }
                newFrag.appendChild(el);
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
        return this.replaceBlock("h1");
    };
    // }}}
    // {{{ Squire.removeHeadline
    Squire.prototype.removeHeadline = function() {
        return this.replaceBlock("p");
    };
    // }}}

    // {{{ Toolbar constructor
    var Toolbar = function(container, options, editor) {
        this.container = container;
        this.eventNamespace = "depageRtfToolbar" + $(this.container).index();
        this.editor = editor;
        this.options = options;
        this.visible = false;
        this.hideTimeout = null;

        this.$toolbar = $("<ul class=\"depageEditorToolbar\"></ul>");
        this.$toolbar.on("click", function(e) {
            e.stopPropagation();
        });
        $(this.container).on("click", function(e) {
            e.stopPropagation();
        });

        this.tagToFunction = {
            b: "inline",
            i: "inline",
            u: "inline",
            s: "inline",
            sub: "inline",
            sup: "inline",
            ul: {
                add: function() { editor.makeUnorderedList(); },
                remove: function() { editor.removeList(); }
            },
            ol: {
                add: function() { editor.makeOrderedList(); },
                remove: function() { editor.removeList(); }
            },
            h1: {
                add: function() { editor.makeHeadline(1); },
                remove: function() { editor.removeHeadline(); }
            }
        };

        for (var i = 0; i < options.allowedTags.length; i++) {
            this.addButton(options.allowedTags[i]);
        }

        this.$toolbar.appendTo(container).hide();

    };
    // }}}
    // {{{ Toolbar.addButton
    Toolbar.prototype.addButton = function(newtag) {
        if (typeof this.tagToFunction[newtag] !== 'undefined') {
            var $button = $("<li class=\"format-" + newtag + "\">" + newtag + "</li>");
            var editor = this.editor;
            var tagToFunction = this.tagToFunction;

            $button
                .appendTo(this.$toolbar)
                .on("click", function() {
                    if (tagToFunction[newtag] == "inline") {
                        if (editor.hasFormat(newtag)) {
                            editor.changeFormat(null, { tag: newtag });
                        } else {
                            editor.changeFormat({ tag: newtag });
                        }
                    } else {
                        if (editor.hasFormat(newtag)) {
                            tagToFunction[newtag].remove();
                        } else {
                            tagToFunction[newtag].add();
                        }
                    }
                });
        }

        return this;
    };
    // }}}
    // {{{ Toolbar.show
    Toolbar.prototype.show = function() {
        this.$toolbar.show();
        this.visible = true;

        var toolbar = this;

        for (var i = 0; i < this.options.allowedTags.length; i++) {
            var tag = this.options.allowedTags[i];
            this.$toolbar.children(".format-" + tag).toggleClass("active", this.editor.hasFormat(tag));
        }

        $("body")
            .off("click." + this.eventNamespace)
            .on("click." + this.eventNamespace, function() {
                toolbar.hide();
            });

        return this;
    };
    // }}}
    // {{{ Toolbar.hide
    Toolbar.prototype.hide = function() {
        var toolbar = this;

        this.visible = false;

        clearTimeout(toolbar.hideTimeout);
        toolbar.hideTimeout = setTimeout(function() {
            if (!toolbar.visible) {
                toolbar.$toolbar.hide();

                $("body")
                    .off("click." + this.eventNamespace);
            }
        }, 200);

        return this;
    };
    // }}}
    // {{{ Toolbar.setPositionByRange
    Toolbar.prototype.setPositionBySelection = function() {
        var range = this.editor.getSelection();
        var rects = range.getClientRects();
        var firstRect = range.getClientRects()[0];
        var secondRect = range.getClientRects()[1] || firstRect;
        var scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
        var scrollLeft = document.documentElement.scrollLeft || document.body.scrollLeft;

        var left = 1000000;
        for (var i = 0; i < rects.length; i++) {
            left = Math.min(left, rects[i].left);
        }

        // @todo check if mouse position is inside rect and position the toolbar next to mouse
        this.$toolbar.offset({
            top: firstRect.top + scrollTop - this.$toolbar.height() * 1.2,
            left: left + scrollLeft
        });

        return this;
    };
    // }}}

    $(document).ready(function () {
        $('.depage-form').each( function() {
            setupForm(this);
        });
    });
})(jQuery);

/* vim:set ft=javascript sw=4 sts=4 fdm=marker : */
