<?php

require_once('validator.php');
require_once('textClass.php');

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

    public function __construct($name, $parameters, $formName) {
        $this->_checkInputName($name);
        $this->_checkInputParameters($parameters);

        $this->type = get_class($this);
        $this->name = $name;
        $this->valid = true;
        $this->formName = $formName; // @todo check?
        $this->label = (isset($parameters['label'])) ? $parameters['label'] : '';
        $this->required = (isset($parameters['required'])) ? $parameters['required'] : false;
        $this->value = (isset($parameters['value'])) ? $parameters['value'] : '';
        $this->classes = array();
        $this->validator = (isset($parameters['validator'])) ? new validator($parameters['validator']) : new validator($this->type);
    }

    public function getName() {
        return $this->name;
    }

    public function validate() {
        $this->valid = (($this->validator->match($this->value)) && ((!empty($this->value)) || (!$this->required)));
    }

    public function isValid() {
        return $this->valid;
    }

    public function setValue($newValue) {
        $this->value = $newValue;
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
