<?php 

require_once('inputClass.php');
require_once('textClass.php');
require_once('checkboxClass.php');

class formClass {
    private $method;
    private $successAddress;
    private $submitButtonLabel = 'go';
    private $inputTypes = array(
        'hidden' => 'text', 
        'text' => 'text',
        'email' => 'text',
        'option' => 'checkbox'
    );
    public $inputs = array();

    public function __construct($submitButtonLabel) {
        $this->submitButtonLabel = $submitButtonLabel;
    }

    public function __call($functionName, $functionArguments) {
        if (substr($functionName, 0, 3) === 'add') {
            $type = strtolower(str_replace('add', '', $functionName)); // @todo case insensitive
            $name = $functionArguments[0];
            $parameters = $functionArguments[1];
            $this->addInput($type, $name, $parameters);
        }
    }

    public function addInput($type, $name, $parameters) {
        $this->checkType($type);
        $this->checkName($name);
        $this->checkParameters($parameters);

        $class = $this->inputTypes[$type] . "Class";
        $this->inputs[] = new $class($type, $name, $parameters);
    }

    private function checkName($name) {
        if (!is_string($name)) {
            throw new Exception('Input name needs to be a string: ' . $name);
        }
        if (trim($name) === '') {
            throw new Exception('Invalid input name');
        }
        foreach($this->inputs as $input) {  
            if ($input->getName() === $name) {
                throw new Exception('Input name already in use: ' . $name);
            }
        }
    }

    private function checkType($type) {
        if (!is_string($type)) {
            throw new Exception('Input type needs to be a string: ' . $type);
        }
        if (!$this->inputTypes[$type]) {
            throw new Exception('Unknown input type: ' . $type);
        }
    }

    private function checkParameters($parameters) {
        if ((isset($parameters)) && (!is_array($parameters))) {
            throw new Exception('Input parameters need to be in an array');
        }
    }
}
