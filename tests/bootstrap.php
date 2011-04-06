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
    
class undefinedMethodException extends \exception {}
?>
