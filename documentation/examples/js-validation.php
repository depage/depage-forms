<?php
/*
 * Load the library...
 */
require_once('../../htmlform.php');

/*
 * Create the example form 'jsExample'
 */
$form = new depage\htmlform\htmlform('jsExample');

/*
 * Add the various input elements to the form by calling the '"add" + element
 * type' method.
 */
$form->addText('username', array('label' => 'User name', 'required' => true));
$form->addEmail('email', array('label' => 'Email address'));
$form->addBoolean('accept', array('label' => 'Accept terms and conditions.', 'required' => true));

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
     * Load the necessary scripts. jQuery, jQuery Tools and the depage-forms
     * customization.
     */
    echo (
        '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>' .
        '<script src="../../lib/js/jquery.tools.min.js"></script>' .
        '<script src="../../lib/js/effect.js"></script>'
    );
    /*
     * Display the form.
     */
    echo ($form);
}
