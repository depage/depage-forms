<?php
/*
 * renders a depage-form form with a richtext element whose content
 * comes from depage-xmldb — the data-dbid attributes of the content
 * have to survive the editor (addDbids option)
 */
require_once __DIR__ . '/../../HtmlForm.php';

$form = new Depage\HtmlForm\HtmlForm('dbidForm', [
    'jsValidation' => 'submit',
]);

$form->addRichtext('html', [
    'label' => 'Richtext',
    'allowedTags' => [
        "a", "b", "i", "p", "br", "h1", "h2", "ul", "ol", "li",
    ],
    'addDbids' => true,
    'defaultValue' => '<p data-dbid="1">test</p>',
]);

echo $form;

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
