<?php

require_once('validator.php');
require_once('textClass.php');
require_once('checkboxClass.php');

abstract class inputClass {
    protected $type;
    protected $name;
    protected $label;
    protected $required;
    protected $formName;
    protected $value;
    protected $validator;
    protected $valid;
    protected $classes;
    protected $defaults = array();

    public function __construct($name, $parameters, $formName) {
        $this->_checkInputName($name);
        $this->_checkInputParameters($parameters);

        $this->type = get_class($this);
        $this->name = $name;
        $this->valid = true;
        $this->formName = $formName;

        $this->setDefaults();

        foreach ($this->defaults as $parameter => $default) {
            $this->$parameter = isset($parameters[$parameter]) ? $parameters[$parameter] : $default;
        }

        $this->validator = (isset($parameters['validator'])) ? new validator($parameters['validator']) : new validator($this->type);
    }

    protected function setDefaults() {
        $this->defaults = array(
            'label' => $this->name,
            'required' => false,
            'requiredChar' => ' *'
        );
    }

    public function getName() {
        return $this->name;
    }

    public function validate() {
        $this->valid = (($this->validator->match($this->value) || empty($this->value)) && (!empty($this->value) || !$this->required));
    }

    public function isValid() {
        return $this->valid;
    }

    public function setValue($newValue) {
        $this->value = $newValue;
    }

    public function getValue() {
        return $this->value;
    }

    public function setRequired() {
        $this->required = true;
    }

    public function setNotRequired() {
        $this->required = false;
    }

    protected function getClasses() {
        $classes = 'input-' . $this->type;
        
        if ($this->required) {
            $classes .= ' required';
        }
        if (!$this->isValid()) {
            $classes .= ' error';
        }
        return $classes;
    }

    protected function getRequiredChar() {
        return ($this->required) ? $this->requiredChar : '';
    }

    private function _checkInputParameters($parameters) {
        if ((isset($parameters)) && (!is_array($parameters))) {
            throw new inputParametersNoArrayException();
        }
    }
    
    private function _checkInputName($name) {
        if (!is_string($name)) {
            throw new inputNameNoStringException();
        }
        if (trim($name) === '') {
            throw new invalidInputNameException();
        }
    }
}
