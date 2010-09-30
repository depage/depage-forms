<?php 

require_once('inputClass.php');
require_once('textClass.php');
require_once('checkboxClass.php');

class formClass {
    private $name;
    private $valid;
    // private $method; @todo implement|
    // private $successAddress; @todo implement|
    private $submitLabel;
    private $inputTypes = array(
        'hidden' => 'text', 
        'text' => 'text',
        'email' => 'text',
        'select' => 'checkbox'
    );
    public $inputs = array();

    public function __construct($name, $parameters = array()) {
        $this->name = $name;
        $this->submitLabel = (isset($parameters['submitLabel'])) ? $parameters['submitLabel'] : 'submit';
        $this->valid = false;
    }

    public function __call($functionName, $functionArguments) {
        if (substr($functionName, 0, 3) === 'add') {
            $type = strtolower(str_replace('add', '', $functionName)); // @todo case insensitive
            $name = $functionArguments[0];
            $parameters = $functionArguments[1];
            $this->addInput($type, $name, $parameters);
        }
    }

    public function addInput($type, $name, $parameters = array()) {
        $this->_checkInputType($type);
        $this->_checkInputName($name);
        $this->_checkInputParameters($parameters);

        $class = $this->inputTypes[$type] . "Class";
        $this->inputs[] = new $class($type, $name, $parameters, $this->name);
    }

    public function __toString() {
        foreach($this->inputs as $input) {
            $renderedForm .= $input;
        }
        $renderedSubmit = '<p id="' . $this->name . '-submit"><input type="submit" name="submit" value="' . $this->submitLabel . '"></p>';
        $renderedForm = '<form id="' . $this->name . '" name="' . $this->name . '">' . $renderedForm . $renderedSubmit . '</form>';
        return $renderedForm;
    }

    public function validate() {
        // @todo ...validate
        $this->valid = true;
    }

    public function isValid() {
        return $this->valid;
    }

    private function _checkFormName($name) {
        if (!is_string($name)) {
            throw new Exception('Form name needs to be a string: ' . $name);
        }
        if (trim($name) === '') {
            throw new Exception('Invalid form name');
        }
    }

    private function _checkInputName($name) {
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

    private function _checkInputType($type) {
        if (!$this->inputTypes[$type]) {
            throw new Exception('Unknown input type: ' . $type);
        }
    }

    private function _checkInputParameters($parameters) {
        if ((isset($parameters)) && (!is_array($parameters))) {
            throw new Exception('Input parameters need to be in an array');
        }
    }
}
