<?php 

require_once ('inputClass.php');

class fileClass extends inputClass {
    public function __construct($type, $name, $parameters, $formName) {
        parent::__construct($type, $name, $parameters, $formName);
    }

    public function render() {
    }
}
