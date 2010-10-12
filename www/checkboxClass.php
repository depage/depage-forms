<?php 

require_once ('inputClass.php');
require_once ('select.php');
require_once ('checkbox.php');
require_once ('radio.php');

abstract class checkboxClass extends inputClass {
    protected $optionList = array();

    public function __construct($name, $parameters, $formName) {
        parent::__construct($name, $parameters, $formName);
        $this->value = (isset($parameters['value'])) ? $parameters['value'] : array();
        $this->optionList = (isset($parameters['optionList'])) ? $parameters['optionList'] : array();
    }
}
