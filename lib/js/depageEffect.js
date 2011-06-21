$.tools.validator.addEffect('depageEffect', function(errors, event) {
        // "show" function
        $.each(errors, function(index, error) {

            // erroneous input paragraph
            var errorParagraph = $(error.input).parents('p');

            // add error notices
            errorParagraph.addClass('error');
            errorParagraph.find('.errorMessage').remove();
            errorParagraph.append('<span class="errorMessage">' +errorParagraph.attr('data-errorMessage')+ '</span>');
            });

        // the effect does nothing when all inputs are valid
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

    if ((check == 'blur') || (check == 'change')) {
        form.validator({
            effect: 'depageEffect',
            inputEvent: check,
            errorInputEvent: check,
        });
    } else if (check == 'submit') {
        form.validator({
            effect: 'depageEffect',
            // do not validate inputs when they are edited
            errorInputEvent: null,
        });
    }
});
