<?php 

require_once('inputClass.php');
require_once('exceptions.php');
require_once('fieldset.php');
require_once('container.php');

class formClass extends container {
    protected $method;
    protected $action;
    protected $successAddress;
    protected $submitLabel;
    protected $sessionSlotName;
    protected $sessionSlot;

    public function __construct($name, $parameters = array()) {
        parent::__construct($name, $parameters);

        if (!session_id()) {
            session_start();
        }
        $this->sessionSlotName = $this->name . '-data';
        $this->sessionSlot =& $_SESSION[$this->sessionSlotName];

        $this->addHidden('form-name', array('value' => $this->name));
    }
    
    protected function setDefaults() {
        parent::setDefaults();
        $this->defaults['submitLabel'] = 'submit';
        $this->defaults['action'] = $_SERVER['REQUEST_URI'];
        $this->defaults['method'] = 'post';
        $this->defaults['successAddress'] = $_SERVER['REQUEST_URI'];
    }

    public function addInput($type, $name, $parameters = array()) {
        $this->checkInputName($name);

        if ($type === 'fieldset') {
            $parameters['form'] = $this;
        }

        $newInput = parent::addInput($type, $name, $parameters);

        $this->loadValueFromSession($name);

        return $newInput;
    }

    public function loadValueFromSession($name) {
        if (isset($this->sessionSlot[$name])) {
            $this->getInput($name)->setValue($this->sessionSlot[$name]);
        }
    }

    public function __toString() {
        $renderedInputs = '';
        foreach($this->inputs as $input) {
            $renderedInputs .= $input;
        }
        $renderedMethod = "method=\"$this->method\"";
        $renderedAction = "action=\"$this->action\"";
        $renderedSubmit = "<p id=\"$this->name-submit\"><input type=\"submit\" name=\"submit\" value=\"$this->submitLabel\"></p>";

        return "<form id=\"$this->name\" name=\"$this->name\" $renderedMethod $renderedAction>$renderedInputs$renderedSubmit</form>";
    }

    public function process() {
        if ((isset($_POST['form-name'])) && (($_POST['form-name']) === $this->name)) {
            $this->_saveToSession();
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
            $this->validate();
        }
    }

    public function validate() {
        $this->onValidate();

        parent::validate();
    }

    public function getInputs() {
        $allInputs = array();
        foreach($this->inputs as $input) {
            if (get_class($input) == 'fieldset') {
                $allInputs = array_merge($allInputs, $input->getInputs());
            } else {
                $allInputs[] = $input;
            }
        }
        return $allInputs;
    }

    public function getInput($name) {
        foreach($this->getInputs() as $input) {
            if ($name === $input->getName()) {
                return $input;
            }
        }
        return false;
    }

    public function getValue($name) {
        return $this->getInput($name)->getValue();
    }

    public function populate($data = array()) {
        foreach($data as $name => $value) {
            $this->getInput($name)->setValue($value);
        }
    }

    public function getValues() {
        return $this->sessionSlot;
    }

    public function checkInputName($name) {
        foreach($this->getInputs() as $input) {  
            if ($input->getName() === $name) {
                throw new duplicateInputNameException();
            }
        }
    }

    public function clearSession() {
        unset($_SESSION[$this->sessionSlotName]);
    }

    protected function onValidate() {
    }

    private function _saveToSession() { // @todo rename (saves to session and input)
        foreach($this->getInputs() as $input) {
            $value = isset($_POST[$input->getName()]) ? $_POST[$input->getName()] : null;
            $this->sessionSlot[$input->getName()] = $value;
            $input->setValue($value);
        }
    }
}
