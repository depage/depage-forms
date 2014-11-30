<?php
    header("Content-type: text/html; charset=UTF-8");
/*
 * Load the library...
 */
require_once '../../HtmlForm.php';

/*
 * Create the example form 'jsRichtext'
 */
$form = new Depage\HtmlForm\HtmlForm('jsRichtext');

/*
 * Add the various input elements to the form by calling the '"add" + element
 * type' method.
 */
$form->addRichtext('html', array(
    'label' => 'Richtext',
    'stylesheet' => '../../lib/css/depage-richtext.css',
    'defaultValue' => "
        <h1>headline h1</h2>
        <p>test</p>
        <p><b>bold</b></p>
        <p><i>italic</i></p>
        <p><i><b>bold/italic</b></i></p>
        <p><a href=\"http://www.google.com\">link</a></p>
        <h2>headline h2</h2>
        <ul>
            <li>unordered list</li>
            <li>text</li>
        </ul>
        <ol>
            <li>ordered list</li>
            <li>text</li>
        </ol>
    ",
));

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
    $values = $form->getValues();
    var_dump($values['html']->saveXML());
    echo('</pre>');

    $form->clearSession();
}

/*
 * Load the necessary scripts. jQuery, jQuery Tools and the depage-forms
 * customization.
 */
?>
<!DOCTYPE html>
<head>
    <link rel="stylesheet" type="text/css" href="../../lib/css/depage-forms.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
    <script src="../../lib/js/jquery.tools.min.js"></script>
    <script src="../../lib/js/depage-richtext.min.js"></script>
    <script src="../../lib/js/effect.min.js"></script>
</head>
<body>
<?php
    /*
     * Display the form.
     */
    echo ($form);
?>
</body>
