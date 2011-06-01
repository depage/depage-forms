<?php
/**
 * Form subclass example
 *
 * It's also possible to override the htmlform class and add elements in the
 * constructor.
 **/

/**
 * Load the library...
 **/
require_once('../../htmlform.php');

/**
 * Example subclass
 **/
class htmlformSubclass extends depage\htmlform\htmlform {
    public function __construct($name, $parameters = array()) {
        parent::__construct($name, $parameters);

        /**
         * Attach elements
         **/
        $form->addText('username', array('label' => 'User name'));
        $form->addEmail('email', array('label' => 'Email address'));
    }
}

/**
 * Instantiate the example subclass 'exampleSubclass'
 **/
$form = new htmlformSubclass('exampleSubclass');

/**
 * The process method is essential to the functionality of the form. It serves
 * various purposes:
 *  - it validates submitted data if there is any
 *  - it redirects to the success page if all the data is valid
 *  - it stores the data in the session and redirects to the form to circumvent
 *    the form resubmission problem
 **/
$form->process();

if ($form->validate()) {
    /**
     * Success, do something useful with the data and clear the session.
     **/
    echo('<pre>');
    var_dump($form->getValues());
    echo('</pre>');

    $form->clearSession();
} else {
    echo ('<link type="text/css" rel="stylesheet" href="test.css">');
    /**
     * Display the form.
     **/
    echo ($form);
}
