<?php
$_SERVER['REQUEST_URI'] = '';
session_start();

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
    
class undefinedMethodException extends \exception {}
?>
