<?php
/*
 * Load the library...
 */
require_once('../../htmlform.php');

/*
 * Create the example form 'stepsExampleForm'
 */
$form = new depage\htmlform\htmlform('stepsExampleForm');

/*
 * Create the first step.
 */
$firstStep = $form->addStep('firstStep');

/*
 * Attach the first text input element
 */
$firstStep->addText('text1', array('label' => 'First step text field'));

/*
 * And so on...
 */
$secondStep = $form->addStep('secondStep');
$secondStep->addText('text2', array('label' => 'Second step text field'));
$thirdStep = $form->addStep('thirdStep');
$thirdStep->addText('text3', array('label' => 'Third step text field'));

/*
 * The process method is essential to the functionality of the form. It serves
 * various purposes:
 *  - it validates submitted data if there is any
 *  - it redirects to the success page if all the data is valid
 *  - it stores the data in the session and redirects to the form to circumvent
 *    the form resubmission problem
 *  - this is also where the form decides which step to display
 */
$form->process();

/*
 * Finally, if the form is valid, dump the data (for demonstration). If it's
 * not valid (or if it hasn't been submitted yet) display the current step.
 */
if ($form->validate()) {
    /*
     * Success, do something useful with the data and clear the session.
     */
    echo('<pre>');
    var_dump($form->getValues());
    echo('</pre>');

    $form->clearSession();
} else {
    echo ('<link type="text/css" rel="stylesheet" href="test.css">');
    /*
     * Display the form (current step).
     */
    echo ($form);
}
