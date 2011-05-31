<?php
/**
 * Simple depage-forms example
 *
 * Contains text and email input elements and demonstrates form processing and
 * validation. In this simple case none of the elements are set required, so an
 * empty form would also be valid.
 **/

/**
 * Load the library...
 **/
require_once('../../htmlform.php');

/**
 * Create the example form 'simpleForm'
 **/
$form = new depage\htmlform\htmlform('simpleForm');

/**
 * Add the various input elements to the form by calling the '"add" + element
 * type' method.
 * The first parameter is the name of the element; it's a unique
 * identifier and therefore required. The optional second parameter is an array
 * of element settings.
 **/
$form->addText('username', array('label' => 'User name'));
$form->addEmail('email', array('label' => 'Email address'));

/**
 * The process method is essential to the functionality of the form. It serves
 * various purposes:
 *  - it validates submitted data if there is any
 *  - it redirects to the success page if all the data is valid
 *  - it stores the data in the session and redirects to the form to circumvent
 *    the form resubmission problem
 **/
$form->process();

/**
 * Finally, if the form is valid, dump the data (for demonstration). If it's
 * not valid (or if it hasn't been submitted yet) display the form.
 **/
if ($form->validate()) {
    /**
     * The getValues method returns an array of the form element values.
     **/
    echo('<pre>');
    var_dump($form->getValues());
    echo('</pre>');
} else {
    echo ('<link type="text/css" rel="stylesheet" href="test.css">');
    /**
     * Display the form.
     **/
    echo ($form);
}
