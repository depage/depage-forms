<?php
/*
 * renders a depage-form form with various element types
 * as a test fixture for the js smoke test
 */
require_once __DIR__ . '/../../HtmlForm.php';

$form = new Depage\HtmlForm\HtmlForm('testForm', [
    'jsValidation' => 'submit',
    'jsAutosave' => true,
]);

$form->addText('username', [
    'label' => 'Username',
    'required' => true,
]);
$form->addEmail('email', [
    'label' => 'Email',
]);
$form->addMultiple('colors', [
    'label' => 'Colors',
    'list' => ['red' => 'red', 'green' => 'green', 'blue' => 'blue'],
]);
$form->addBoolean('accept', [
    'label' => 'Accept',
]);
$form->addSingle('size', [
    'label' => 'Size',
    'list' => ['s' => 'small', 'l' => 'large'],
]);

echo $form;
