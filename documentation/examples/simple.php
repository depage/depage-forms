<?php
/*
 * Load the library...
 */
require_once('../../htmlform.php');

/*
 * Create the example form 'simpleForm'
 */
$form = new depage\htmlform\htmlform('simpleForm');

/*
 * Add the various input elements to the form by calling the '"add" + element
 * type' method.
 * The first parameter is the name of the element; it's a unique
 * identifier and therefore required. The optional second parameter is an array
 * of element settings.
 */
$form->addText('username', array('label' => 'User name', 'required' => true));
$form->addEmail('email', array('label' => 'Email address'));

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
