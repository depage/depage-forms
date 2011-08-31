<?php
/*
 * Load the library...
 */
require_once('../../htmlform.php');

/*
 * Create the example form 'userInfoExample'. This time, instead of the default
 * 'submit', the button text should say 'Send'.
 */
$form = new depage\htmlform\htmlform('userInfoExample', array('label' => 'Send'));

/*
 * Create subgroup of data elements
 */
$dataFieldset = $form->addFieldset('dataFieldset', array('label' => 'Personal Information'));

/*
 * First & last name fields. Nothing special here.
 */
$dataFieldset->addText('firstName', array('label' => 'First name'));
$dataFieldset->addText('lastName', array('label' => 'Last name'));

/*
 * User name field, required by the form. Contains custom validator with a
 * regular expression. The title text is displayed on mousover; it should
 * usually explain the validator pattern.
 */
$dataFieldset->addText('userName', array('label' => 'User name', 'required' => true, 'validator' => '/.{6,}/', 'title' => 'at least 6 characters'));

/*
 * Email field, required by the form.
 */
$dataFieldset->addEmail('email', array('label' => 'Email address', 'required' => true));

/*
 * Language selection
 */
$dataFieldset->addSingle('language', array(
    'label' => 'Language',
    'skin'  => 'select',
    'list'  => array(
        'en' => 'English',
        'es' => 'Spanish',
        'fr' => 'French',
        'de' => 'German',
    ),
));

/*
 * Subgroup for the legal stuff
 */
$checkFieldset = $form->addFieldset('checkFieldset', array('label' => 'Terms and Conditions'));

/*
 * Terms & conditions checkboxes
 *
 * When they're set required, they have to be checked to continue
 */
$checkFieldset->addBoolean('read', array('label' => 'I have read and understood the terms and conditions.', 'required' => true));
$checkFieldset->addBoolean('accept', array('label' => 'Accept terms and conditions.', 'required' => true));

/*
 * The process method is essential to the functionality of the form. It serves
 * various purposes:
 *  - it validates submitted data if there is any
 *  - it redirects to the success page if all the data is valid
 *  - it stores the data in the session and redirects to the form to circumvent
 *    the form resubmission problem
 */
$form->process();

if ($form->validate()) {
    /*
     * Success, do something useful with the data and clear the session.
     */
    echo('<a href="">back</a>');
    echo('<pre>');
    var_dump($form->getValues());
    echo('</pre>');

    /*
     * Generally, one would acceѕs the form data like this...
     */
    $data       = $form->getValues();
    $username   = $data['userName'];

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
