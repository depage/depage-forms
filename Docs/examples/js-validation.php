<?php
/*
 * Load the library...
 */
require_once '../../HtmlForm.php';

/*
 * Create the example form 'jsExample'
 */
$form = new Depage\HtmlForm\HtmlForm('jsExample');

/*
 * Add the various input elements to the form by calling the '"add" + element
 * type' method.
 */
$form->addText('username', [
    'label' => 'User name',
    'required' => true,
]);
$form->addEmail('email', [
    'label' => 'Email address',
]);
$form->addBoolean('accept', [
    'label' => 'Accept terms and conditions.',
    'required' => true,
]);

/*
 * The validator pattern has to match the complete string (HTML5 spec). Here
 * are some examples:
 */
$form->addText('singleLetter', [
    'label' => 'Single letter',
    'required' => true,
    'validator' => '/[a-zA-Z]/',
]);
$form->addText('letters', [
    'label' => 'One ore more letters',
    'required' => true,
    'validator' => '/[a-zA-Z]+/',
]);

/*
 * The process method is essential to the functionality of the form. It serves
 * various purposes:
 *  - it validates submitted data if there is any
 *  - it redirects to the success page if all the data is valid
 *  - it stores the data in the session and redirects to the form to circumvent
 *    the form resubmission problem
 */
$form->process();

/*
 * Finally, if the form is valid, dump the data (for demonstration). If it's
 * not valid (or if it hasn't been submitted yet) display the form.
 */
if ($form->validate()) {
    /*
     * Success, do something useful with the data and clear the session.
     * The getValues method returns an array of the form element values.
     */
    echo('<a href="">back</a>');
    echo('<pre>');
    var_dump($form->getValues());
    echo('</pre>');

    $form->clearSession();
} else {
    /*
     * Load the necessary scripts. The depage-forms standalone bundle
     * enhances the form: validation, autosave and richtext support.
     * (Use dist/depage-form-jquery.js instead if you prefer the
     * jQuery plugin API.)
     */
    ?>
<!DOCTYPE html>
<head>
    <link rel="stylesheet" type="text/css" href="../../dist/depage-forms.css">
    <script src="../../dist/depage-form.js"></script>
</head>
<body>
<?php
        /*
         * Display the form.
         */
        echo($form);
    ?>
</body>
<?php
}
