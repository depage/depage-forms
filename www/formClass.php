<?php 

require_once('inputClass.php');
require_once('exceptions.php');

class formClass {
    private $name;
    private $valid;
    private $method;
    private $action;
    private $successAddress;
    private $submitLabel;
    private $inputs = array();
    private $sessionSlot;

    public function __construct($name, $parameters = array()) {
        $this->_checkFormName($name);

        $this->name = $name;
        $this->submitLabel = (isset($parameters['submitLabel'])) ? $parameters['submitLabel'] : 'submit';
        $this->action = (isset($parameters['action'])) ? $parameters['action'] : '';
        $this->method = ((isset($parameters['method'])) && (strToLower($parameters['method'])) === 'get') ? 'get' : 'post'; // @todo make this more verbose 
        $this->successAddress = (isset($parameters['successAddress'])) ? $parameters['successAddress'] : $_SERVER['REQUEST_URI'];
        
        if (!session_id()) {
            session_start();
        }

        $this->sessionSlot =& $_SESSION[$this->name . '-data'];
        $this->addHidden('form-name', array('value' => $this->name));
    }

    public function __call($functionName, $functionArguments) {
        if (substr($functionName, 0, 3) === 'add') {
            $type = strtolower(str_replace('add', '', $functionName)); // @todo case insensitive
            $name = (isset($functionArguments[0])) ? $functionArguments[0] : '';
            $parameters = (isset($functionArguments[1])) ? $functionArguments[1] : array();
            $this->addInput($type, $name, $parameters);
        }
    }

    public function addInput($type, $name, $parameters = array()) {
        $this->_checkInputName($name);
        $this->_checkInputType($type);

        $this->inputs[] = new $type($name, $parameters, $this->name);
    }

    public function __toString() {
        $renderedInputs = '';
        foreach($this->inputs as $input) {
            $renderedInputs .= $input;
        }
        $renderedMethod = ' method="' . $this->method . '"';
        $renderedAction = (empty($this->action)) ? '' : ' action="' . $this->action . '"';
        $renderedSubmit = '<p id="' . $this->name . '-submit"><input type="submit" name="submit" value="' . $this->submitLabel . '"></p>'; // @todo clean up

        $renderedForm = '<form id="' . $this->name . '" name="' . $this->name . '"' . $renderedMethod . $renderedAction . '>' . $renderedInputs . $renderedSubmit . '</form>';
        return $renderedForm;
    }

    public function process() {
        if ((isset($_POST['form-name'])) && (($_POST['form-name']) === $this->name)) {
            $this->saveToSession();
            $this->loadFromSession();
            $this->validate();
            if ($this->valid) {
                header('Location: ' . $this->successAddress);
                die( "Tried to redirect you to " . $this->successAddress);
            } else {
                header('Location: ' . $_SERVER['REQUEST_URI']);
                die( "Tried to redirect you to " . $_SERVER['REQUEST_URI']);
            }
        }
        if (isset($this->sessionSlot)) {
            $this->loadFromSession();
            $this->validate();
        }
    }

    public function validate() {
        $this->valid = true;
        foreach($this->inputs as $input) {
            $input->validate();
            $this->valid = (($this->valid) && ($input->isValid()));
        }
    }

    public function isValid() {
        return $this->valid;
    }

    public function getInputs() {
        return $this->inputs;
    }

    public function getInputIndex($name) {
        foreach($this->inputs as $index => $input) {
            if ($name === $input->getName()) {
                return $index;
            }
        }
        return false;
    }

    public function populate($data = array()) {
        foreach($data as $name => $value) {
            $this->inputs[$this->getInputIndex($name)]->setValue($value);
        }
    }

    private function loadFromSession() {
        foreach($this->inputs as $input) {
            if (isset($this->sessionSlot[$input->getName()]['value'])) {
                $input->setValue($this->sessionSlot[$input->getName()]['value']);
            }
        }
    }

    private function saveToSession() {
        foreach($this->inputs as $input) {
            if (isset($_POST[$input->getName()])) {
                $this->sessionSlot[$input->getName()]['value'] = $_POST[$input->getName()];
            }
        }
    }

    private function _checkFormName($name) {
        if (!is_string($name)) {
            throw new formNameNoStringException();
        }
        if (trim($name) === '') {
            throw new invalidFormNameException();
        }
    }

    private function _checkInputName($name) {
        foreach($this->inputs as $input) {  
            if ($input->getName() === $name) {
                throw new duplicateInputNameException();
            }
        }
    }

    private function _checkInputType($type) {
        if (!class_exists($type)) {
            throw new unknownInputTypeException();
        }
    }
}
