<?php
/*
 * Load the library...
 */
require_once '../../HtmlForm.php';

/*
 * Example subclass
 */
class HtmlFormSubclass extends Depage\HtmlForm\HtmlForm
{
    public function __construct($name, $parameters = array())
    {
        parent::__construct($name, $parameters);

        /*
         * Attach elements
         */
        $this->addText('username', array('label' => 'User name'));
        $this->addEmail('email', array('label' => 'Email address'));
    }

    /*
     * Say, we want the email address field to be required if the user has
     * entered a user name. This can be done by overriding the onValidate()
     * method with a custom rule.
     */
    public function onValidate()
    {
        /*
         * If the 'username' field is not empty, set the 'email' field
         * required.
         */
        if (!$this->getElement('username')->isEmpty()) {
            $this->getElement('email')->setRequired();
        }

        return parent::onValidate();
    }
}

/*
 * Instantiate the example subclass 'exampleSubclass'
 */
$form = new HtmlFormSubclass('exampleSubclass');

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
