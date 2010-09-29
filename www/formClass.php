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
        if (substr($functionName, 0, 3) == 'add') {
            $type = strtolower(str_replace('add', '', $functionName)); // @todo case insensitive
            $name = $functionArguments[0];
            $parameters = $functionArguments[1];
            $this->addInput($type, $name, $parameters);
        }
    }

    public function addInput($type, $name, $parameters = array()) {
        if ($this->inputTypes[$type]) {
            $class = $this->inputTypes[$type] . "Class";
            foreach($this->inputs as $input) {  // @todo $name as key?
                if ($input->getName() == $name) {
                    throw new Exception('Element name already in use: ' . $name);
                }
            }
            $this->inputs[] = new $class($type, $name, $parameters);
        } else {
            throw new Exception('Unknown input type: ' . $type);
        }
    }
}
