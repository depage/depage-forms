<?php
/**
 * Redirect to success page example
 *
 * Once the form is validated, it can also be redirected to another URL. This
 * can be done by setting the 'successURL' parameter in the form.
 **/

/**
 * Load the library...
 **/
require_once('../../htmlform.php');

/**
 * Create the example form 'originForm' with different success page url.
 **/
$form = new depage\htmlform\htmlform('originForm', array('successURL' => 'success.php'));

/**
 * Create input elements
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
 * Display the form.
 **/
echo ($form);
