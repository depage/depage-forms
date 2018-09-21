<?php

require_once '../../HtmlForm.php';

$form = new Depage\HtmlForm\HtmlForm('uploader');
$form->addHidden('APC_UPLOAD_PROGRESS', array('defaultValue' => 'xxx'));
$form->addFile('file', array('label' => 'File', 'required' => true));
$form->process();

if ($form->validate()) {

    echo('<a href="">back</a>');
    echo('<pre>');
    var_dump($form->getValues());
    echo('</pre>');

    $form->clearSession();
} else {
?>
<!DOCTYPE html>
<head>
    <link rel="stylesheet" type="text/css" href="../../lib/css/depage-forms.css">
    <script src="../../lib/js/jquery.js"></script>
    <script src="../../lib/js/jquery.tools.min.js"></script>
    <script src="../../lib/js/depage-uploader.js"></script>
    <script src="../../lib/js/effect.js"></script>
</head>
<body>
    <?php // echo($form); ?>
    <form id="uploader" name="uploader" class="depage-form" method="post" action="/documentation/examples/uploader.php" enctype="multipart/form-data">
        <input name="APC_UPLOAD_PROGRESS" id="APC_UPLOAD_PROGRESS" type="hidden" class="input-hidden" value="xxx">
        <input id="file" name="file" type="file">
    </form>

</body>
<?php
}
