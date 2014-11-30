<?php
/*
 * Load the library...
 */
require_once '../../HtmlForm.php';

/*
 * Create the example form 'populateForm'
 */
$form = new Depage\HtmlForm\HtmlForm('populateForm');

/*
 * Create input elements
 */
$form->addText('username', array('label' => 'User name'));
$form->addEmail('email', array('label' => 'Email address'));

/*
 * The populate method selects input elements by name. Hence, elements in
 * fieldsets/steps are accessed exactly like elements directly attached to the
 * form.
 */
$fieldset = $form->addFieldset('fieldset', array('label' => 'User name'));
$fieldset->addText('fieldsetText', array('label' => 'Text in fieldset'));

/*
 * Parsing an associative array to the populate method
 * ('element name' => value).
 */
$form->populate(
    array(
        'username'      => 'depage',
        'email'         => 'mail@somedomain.org',
        'fieldsetText'  => 'It works!',
    )
);

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
