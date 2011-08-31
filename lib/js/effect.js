/*
 * @require framework/shared/jquery-1.4.4.js
 * @require framework/htmlform/lib/js/jquery.tools.min.js
 * @require framework/htmlform/lib/js/depage-richtext.js
 */
$.tools.validator.addEffect('depageEffect', function(errors, event) {
    // "show" function
    $.each(errors, function(index, error) {
        // erroneous input paragraph
        var errorParagraph = $(error.input).parents('p');

        // if there's no error message
        if (errorParagraph.find('.errorMessage').length === 0) {
            // add error notices
            errorParagraph.append('<span class="errorMessage">' + errorParagraph.attr('data-errorMessage')+ '</span>');
            errorParagraph.addClass('error');
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

$(document).ready(function () {
    $('.depage-form').each( function() {
        var form = this;
        var $form = $(this);
        var check = $form.attr('data-jsvalidation');
        var autosave = $form.attr('data-jsautosave');

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
        
        // autosave
        if (autosave == "true") {
            var saveInterval = 1000;
            var now = new Date();

            form.data = form.data || {};
            form.data.saving = false;
            form.data.lastsave = now.getTime();
            form.data.timer = null;

            form.data.autosave = function() {
                var data = {};

                $("input, select", form).each( function () {
                    console.log("input: " + this.name + " = " + this.value);
                    data[this.name] = this.value;
                });

                data['form-autosave'] = "true";
                form.data.saving = true;

                $.post(form.action, data, function(response, textStatus) {
                    now = new Date();

                    form.data.lastsave = now.getTime();
                    form.data.saving = false;

                    console.log("saved");
                    //console.log(response);
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

            $("input, select", form).change( function() {
                form.data.changed(true);
            });
            /*
            // only for textarea and richtext
            .keyup( function() {
                form.data.changed();
            });
            */

        }

        // add richtext-editor to richtext inputs
        $('.input-richtext', form).each(function() {
            var options = $(this).data('richtext-options');
            var $textarea = $("textarea", this);

            $textarea.depageEditor(options);
        });
    });
});
