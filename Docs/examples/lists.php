<?php
/*
 * Load the library...
 */
require_once '../../HtmlForm.php';

/*
 * Create the example form 'listsExampleForm'
 */
$form = new Depage\HtmlForm\HtmlForm('listsExampleForm');

/*
 * Multiple input element with checkbox skin, set required
 *
 * Contrary to HTML5 specs, a required multiple-checkbox only needs one item
 * checked to be valid.
 */
$form->addMultiple('multipleCheckbox', [
    'required' => true,
    'label' => 'Multiple-checkbox',
    'list' => [
        'option one'    => 'label one',
        'option two'    => 'label two',
        'option three'  => 'label three',
    ],
]);

/*
 * Non-associative lists arrays
 *
 * The list parameter can also be a normal array (it doesn't have to be
 * associative). In that case it returns the array key ("0", "1", "2"). This
 * works for every element with an option list ie. text, single and multiple.
 */
$form->addMultiple('multipleSelect', [
    'label' => 'Multiple-select',
    'skin' => 'select',
    'list' => [
        'label one',
        'label two',
        'label three',
    ],
]);

/*
 * Single input element with radio-skin, set required
 *
 * Single is basically the same as multiple. Instead of checkboxes
 * you'll get radiobuttons, so you can only select on item.
 */
$form->addSingle('singleCheckbox', [
    'required' => true,
    'label' => 'Single-radiobutton',
    'list' => [
        'option one'    => 'label one',
        'option two'    => 'label two',
        'option three'  => 'label three',
    ],
]);

/*
 * Single with select skin
 *
 * The single-element rendered as a select-list instead of radion-buttons
 */
$form->addSingle('singleSelect', [
    'label' => 'Single-select',
    'skin' => 'select',
    'list' => [
        'label one',
        'label two',
        'label three',
    ],
]);

/*
 * Optgroups
 *
 * 'select'-skin elements support optgroups. To use them simply add another
 * array dimension. This works for single and multiple elements with the
 * 'select' skin.
 */
$form->addSingle('optgroupSingle', [
    'label' => 'Optgroup-single',
    'skin' => 'select',
    'list' => [
        'group one' => [
            'option one'    => 'label one',
            'option two'    => 'label two',
            'option three'  => 'label three',
        ],
        'group two' => [
            'option four'   => 'label four',
            'option five'   => 'label five',
        ],
    ],
]);

/*
 * Datalists
 *
 * Modern browsers support datalists for text fields as input examples. Usage
 * is similar to single or multiple element lists.
 * Associative arrays (option => label) also work.
 */
$form->addText('datalistText', [
    'label' => 'Text with datalist',
    'list' => [
        'option one',
        'option two',
        'option three',
    ],
]);

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
