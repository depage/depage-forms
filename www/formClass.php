<?php 

require_once('inputClass.php');
require_once('textClass.php');
require_once('checkboxClass.php');

class formClass {
    private $name;
    private $valid;
    private $method;
    private $action;
    private $successAddress;
    private $submitLabel;
    private $inputTypes = array(
        'hidden' => 'text', 
        'text' => 'text',
        'email' => 'text',
        'select' => 'checkbox'
    );
    public $inputs = array();

    public function __construct($name, $parameters = array()) {
        $this->_checkFormName($name);
        session_start();

        $this->name = $name;
        $this->submitLabel = (isset($parameters['submitLabel'])) ? $parameters['submitLabel'] : 'submit';
        $this->action = (isset($parameters['action'])) ? $parameters['action'] : '';
        $this->method = (strToLower($parameters['method']) === 'get') ? 'get' : 'post'; // @todo make this more verbose 
        $this->successAddress = (isset($parameters['successAddress'])) ? $parameters['successAddress'] : $_SERVER['REQUEST_URI']; // @todo url check?
        $this->valid = ($_SESSION[$this->name . '-valid'] === true) ? true : false;
        
        //$this->addHidden('PHPSESSID', array('value' => session_id())); @todo required?
        $this->addHidden('form-name', array('value' => $name));
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

        $class = $this->inputTypes[$type] . "Class";
        $this->inputs[] = new $class($type, $name, $parameters, $this->name);
    }

    public function __toString() {
        foreach($this->inputs as $input) {
            $renderedForm .= $input;
        }
        $renderedSubmit = '<p id="' . $this->name . '-submit"><input type="submit" name="' . $this->name . '-submit" value="' . $this->submitLabel . '"></p>'; // @todo clean up
        $renderedForm = '<form id="' . $this->name . '" name="' . $this->name . '" method="' . $this->method . '" action="' . $this->action . '">' . $renderedForm . $renderedSubmit . '</form>';
        return $renderedForm;
    }

    public function validate() {

        if (($_POST['form-name']) === $this->name) {
            $_SESSION[$this->name . '-data'] = $_POST;
            if ($_SESSION[$this->name . '-data']['firstname'] == 'valid') {
                $_SESSION[$this->name . '-valid'] = true;
                header('Location: ' . $this->successAddress);
                die( "Tried to redirect you to " . $this->successAddress);
            } else {
                header('Location: ' . $_SERVER['REQUEST_URI']);
                die( "Tried to redirect you to " . $_SERVER['REQUEST_URI']);
            }
        }
        // @todo ...validate
    }

    public function isValid() {
        return $this->valid;
    }

    public function populate() {
        
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
}
