<?php
$_SERVER['REQUEST_URI'] = 'http://www.depagecms.net/';
session_start();

require_once('../htmlform.php');

class logTestClass {
    public $error = array(
        'argument'  => '',
        'type'      => '',
    );

    public function log($argument, $type) {
        $this->error = array(
            'argument'  => $argument,
            'type'      => $type,
        );
    }
}

class nameTestForm {
    public function getName() {
        return 'formName';
    }

    public function checkElementName() {}
    public function updateInputValue() {}
}

class undefinedMethodException extends \exception {}
?>
