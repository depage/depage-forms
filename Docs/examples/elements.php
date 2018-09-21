<?php
/*
 * Load the library...
 */
require_once '../../HtmlForm.php';

/*
 * Create the example form 'showcaseElements'
 */
$form = new Depage\HtmlForm\HtmlForm('showcaseElements');

/*
 * Boolean input element
 *
 * Single checkbox element, usually used for 'accept terms and conditions' type
 * checkboxes (when set required). When set required, it has to be checked in
 * order to pass validation. Returns boolean value.
 */
$form->addBoolean('Boolean');

/*
 * Email input element
 *
 * Text input element with email validator. Returns string value.
 */
$form->addEmail('Email');

/*
 * Multiple input element with checkbox skin
 *
 * By default, the multiple input element is a HTML checkbox where more than
 * one option can be selected. Returns array of option strings.
 */
$form->addMultiple('multipleCheckbox', array(
    'label' => 'Multiple-checkbox',
    'list' => array(
        'option one'    => 'label one',
        'option two'    => 'label two',
        'option three'  => 'label three',
    ),
));

/*
 * Multiple input element with select skin
 *
 * When the skin option is set to 'select', the multiple input element is a HTML
 * select element where more than one option can be selected. Returns array of
 * option strings.
 */
$form->addMultiple('multipleSelect', array(
    'label' => 'Multiple-select',
    'skin' => 'select',
    'list' => array(
        'option one'    => 'label one',
        'option two'    => 'label two',
        'option three'  => 'label three',
    ),
));

/*
 * Number input element
 *
 * By default, min, max, step values aren't set (Depend on browser, step is
 * usually 1). Returns float value.
 */
$form->addNumber('Number', array(
    'min'   => 0,
    'max'   => 10,
    'step'  => 2,
));

/*
 * Password input element
 *
 * Text input element that displays only asterisks or bullets (depends on the
 * browser). Returns string value.
 */
$form->addPassword('Password');

/*
 * Range input element
 *
 * Same as number input element but with a slider interface.
 */
$form->addRange('Range', array(
    'min'   => 0,
    'max'   => 10,
));

/*
 * Single input element with radio skin
 *
 * By default the single element is a HTML radio element (only one option can
 * be slected). Returns string value of selected option.
 */
$form->addSingle('singleRadio', array(
    'label' => 'Single-radio',
    'list' => array(
        'option one'    => 'label one',
        'option two'    => 'label two',
        'option three'  => 'label three',
    ),
));

/*
 * Single input element with select skin
 *
 * When the skin option is set to 'select' the single input element is a HTML
 * select element (only one option can be selected). Returns string value of
 * selected option.
 */
$form->addSingle('SingleSelect', array(
    'label' => 'Single-select',
    'skin' => 'select',
    'list' => array(
        'option one'    => 'label one',
        'option two'    => 'label two',
        'option three'  => 'label three',
    ),
));

/*
 * Tel input element
 *
 * Text input element with telephone number validator. Returns string value.
 */
$form->addTel('Tel');

/*
 * Text input element
 *
 * Standard HTML text element. Returns string value.
 */
$form->addText('Text');

/*
 * Textarea input element
 *
 * Standart HTML textarea element. Returns string value.
 */
$form->addTextarea('Textarea');

/*
 * URL input element
 *
 * Text input element with URL validator. Returns string value.
 */
$form->addUrl('URL');

/*
 * Fieldset container element
 *
 * Fieldsets can be used to create subgroups of elements. In this example it
 * contains a single text element.
 */
$fieldset = $form->addFieldset('Fieldset');
$fieldset->addText('Text2');

/*
 * Creditcard input
 *
 * return values as string named with the name of the input as prefix, e.g.:
 *
 *     - {prefix}_card_type
 *     - {prefix}_card_number
 *     - {prefix}_card_numbercheck
 *     - {prefix}_card_expirydate
 *     - {prefix}_card_owner
 */
$form->addCreditcard('creditcard');

/*
 * HTML element
 *
 * Adds HTML code.
 */
$form->addHtml('Custom <b>HTML</b> element');

$form->process();

if ($form->validate()) {
    /*
     * Success, do something useful with the data and clear the session.
     */
    echo('<a href="">back</a>');
    echo('<pre>');
    var_dump($form->getValues());
    echo('</pre>');

    $form->clearSession();
} else {
    /*
     * Display the form.
     */
?>
<!DOCTYPE html>
<head>
    <link rel="stylesheet" type="text/css" href="../../lib/css/depage-forms.css">
</head>
<body>
    <?php echo($form); ?>
</body>
<?php
}
