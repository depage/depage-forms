<?php 

require_once ('inputClass.php');
require_once ('select.php');
require_once ('checkbox.php');

class checkboxClass extends inputClass {
    public function __construct($name, $parameters, $formName) {
        parent::__construct($name, $parameters, $formName);
        $this->value = (isset($parameters['value'])) ? $parameters['value'] : array();
    }
}
