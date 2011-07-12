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
        if (errorParagraph.find('.errorMessage').length == 0) {
            // add error notices
            errorParagraph.append('<span class="errorMessage">' +errorParagraph.attr('data-errorMessage')+ '</span>');
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
    var form = $('.depage-form');
    var check = form.attr('data-jsValidation');

    // validate on blur or change
    if ((check == 'blur') || (check == 'change')) {
        form.validator({
            effect: 'depageEffect',
            inputEvent: check,
            errorInputEvent: check
        });
    // validate on submit
    } else if (check == 'submit') {
        form.validator({
            effect: 'depageEffect',
            errorInputEvent: null,
        });
    }

    // add richtext-editor to richtext inputs
    $('.input-richtext', form).each(function() {
        var options = $(this).data('richtext-options');
        var $textarea = $("textarea", this);

        $textarea.depageEditor(options);
    });

});
