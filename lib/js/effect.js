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
        $form.bind("onFail", function(e, errors) {
            // error found
            $form.find(".submit").addClass("error");
        });
        $form.bind("onSuccess", function(e, errors) {
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
        $form.delegate('.error input', 'focus', function(event) {
            $(this).parents('.error').find('.errorMessage').hide();
        });
        // }}}
        // {{{ focus input on click on error message
        $form.delegate('.errorMessage', 'click', function(event) {
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

            $label.bind('click', function(e) {
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

            if (options.autogrow && !$.browser.msie) {
                $textarea.autogrow = function() {
                    $textareaSize[0].value = $textarea[0].value;

                    // set new height
                    var newHeight = $textareaSize[0].scrollHeight + 30;
                    $textarea.height(newHeight);
                };
                $textarea.mouseup(function() {
                }).keyup( function() {
                    $textarea.autogrow();
                }).keydown( function() {
                    $textarea.autogrow();
                }).scroll( function() {
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
        $.extend(options, $.parseJSON($(container).attr('data-textarea-options')), $.parseJSON($(container).attr('data-richtext-options')));

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

        var $toolbar = setupRichtextToolbar(container, options, editor).hide();

        $(editor).on("input", function() {
            // @todo don't call this on every change
            $input[0].value = editor.getHTML();
            console.log("input");
            setToolbarVisibility($toolbar, false);
        });
        $(editor).on("blur", function() {
            console.log("blur");
            setToolbarVisibility($toolbar, false);
        });
        $(editor).on("select", _.debounce(function() {
            var range = editor.getSelection();
            var firstRect = range.getClientRects()[0];
            var scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
            var scrollLeft = document.documentElement.scrollLeft || document.body.scrollLeft;

            console.log("select");

            $toolbar
                .show()
                .offset({
                    top: firstRect.top + scrollTop - 70,
                    left: firstRect.left + scrollLeft + firstRect.width / 2
                });
            setToolbarVisibility($toolbar, true);
        }, 200));
    }
    // }}}
    // {{{ setupRichtextToolbar()
    function setupRichtextToolbar(container, options, editor) {
        var $toolbar = $("<ul class=\"depageEditorToolbar\"></ul>");

        if (options.allowedTags.indexOf("b") != -1) {
            addToolbarButton($toolbar, "b", function() {
                if (editor.hasFormat("b")) {
                    editor.removeBold();
                } else {
                    editor.bold();
                }
                return false;
            });
            addToolbarButton($toolbar, "i", function() {
                if (editor.hasFormat("i")) {
                    editor.removeItalic();
                } else {
                    editor.italic();
                }
                return false;
            });
        }
        $toolbar.appendTo(container);

        // @todo set click event outside of toolbar

        return $toolbar;
    }
    // }}}
    // {{{ addToolbarButton
    function addToolbarButton($toolbar, button, func) {
        var $button = $("<li>" + button + "</li>");
        $button
            .appendTo($toolbar)
            .on("click", func);
    }
    // }}}
    // {{{ setToolbarVisibility()
    function setToolbarVisibility($toolbar, visibility) {
        if (visibility) {
            $toolbar.show();
        } else {
            $toolbar.hide();
        }
    }
    // }}}

    $(document).ready(function () {
        $('.depage-form').each( function() {
            setupForm(this);
        });
    });
})(jQuery);

/* vim:set ft=javascript sw=4 sts=4 fdm=marker : */
