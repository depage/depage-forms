$.tools.validator.addEffect('depageEffect', function(errors, event) {
        // remove old error notices
        $('.depage-form .errorMessage').remove();
        $('.depage-form .error').removeClass('error');

        // "show" function
        $.each(errors, function(index, error) {

            // erroneous input paragraph
            var errorParagraph = $(error.input).parents('p');

            // add error notices
            errorParagraph.addClass('error');
            errorParagraph.append('<span class="errorMessage">' +errorParagraph.attr('data-errorMessage')+ '</span>');
            });

        // the effect does nothing when all inputs are valid
        }, function(inputs) {
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
