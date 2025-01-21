<?php

$_SERVER['REQUEST_URI'] = 'http://www.depagecms.net/';
session_start();

// using the autoloader from the main class
require_once(__DIR__ . '/../HtmlForm.php');

// {{{ logTestClass
/**
 * Dummy test class (for element & validator tests)
 **/
class logTestClass
{
    public $error = [
        'argument'  => '',
        'type'      => '',
    ];

    public function log($argument, $type = null)
    {
        $this->error = [
            'argument'  => $argument,
            'type'      => $type,
        ];
    }
}
// }}}

// {{{ nameTestForm
/**
 * Dummy form class
 **/
class nameTestForm
{
    public function getName()
    {
        return 'formName';
    }

    public function checkElementName() {}
    public function updateInputValue() {}
    public function getNamespaces()
    {
        return ['\\Depage\\HtmlForm\\Elements'];
    }
}
// }}}
//
// {{{ csrfTestForm
/**
 * Dummy form class
 **/
class csrfTestForm extends Depage\HtmlForm\HtmlForm
{
    protected function getNewCsrfToken()
    {
        return "xxxxxxxx";
    }
}
// }}}
// {{{ htmlformTestClass
/**
 * Custom htmlform class with overidden redirect method for easier testing
 **/
class htmlformTestClass extends csrfTestForm
{
    public $testRedirect;
    public $testLog;

    public function redirect($url): void
    {
        $this->testRedirect = $url;
    }

    public function log($message, $type = null): void
    {
        $this->testLog = $message;
    }
}
// }}}

