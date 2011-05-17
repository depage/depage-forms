<?php
$_SERVER['REQUEST_URI'] = 'http://www.depagecms.net/';
session_start();

// using the autoloader from the main class
require_once('../htmlform.php');

// {{{ logTestClass
/**
 * Dummy test class (for element & validator tests)
 **/
class logTestClass {
    public $error = array(
        'argument'  => '',
        'type'      => '',
    );

    public function log($argument, $type = null) {
        $this->error = array(
            'argument'  => $argument,
            'type'      => $type,
        );
    }
}
// }}}

// {{{ nameTestForm
/**
 * Dummy form class
 **/
class nameTestForm {
    public function getName() {
        return 'formName';
    }

    public function checkElementName() {}
    public function updateInputValue() {}
}
// }}}
?>
