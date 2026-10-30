<?php
/*
 * renders a depage-form form with richtext and textarea elements
 * as a test fixture for the js smoke test
 */
require_once __DIR__ . '/../../HtmlForm.php';

$form = new Depage\HtmlForm\HtmlForm('rtForm', [
    'jsValidation' => 'submit',
]);

$form->addRichtext('html', [
    'label' => 'Richtext',
    'allowedTags' => [
        "a", "b", "i", "p", "br", "h1", "h2", "ul", "ol", "li",
    ],
    'defaultValue' => "<p>test</p>",
]);
$form->addTextarea('note', [
    'label' => 'Note',
    'autogrow' => true,
]);
$form->addSingle('fruit', [
    'label' => 'Fruit',
    'skin' => 'select',
    'list' => ['apple' => 'Apple', 'banana' => 'Banana'],
]);

echo $form;
