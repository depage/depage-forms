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
        $this->classes = array('input-' . $this->type);
        $this->validator = (isset($parameters['validator'])) ? new validator($parameters['validator']) : new validator($this->type);
    }

    public function getName() {
        return $this->name;
    }

    public function __toString() {
        $renderedClass = ' class="';
        foreach($this->getClasses() as $class) {
            $renderedClass .= $class . ' ';
        }
        $renderedClass[strlen($renderedClass)-1] = '"';

        $renderedUniqueId = ' id="' . $this->formName . '-' . $this->name . '"';
        return '<p' . $renderedUniqueId . $renderedClass . '>' . $this->render() . '</p>';
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

    private function getClasses() {
        if ($this->required) {
            $this->classes[] = 'required';
        }
        if (!$this->isValid()) {
            $this->classes[] = 'error';
        }
        return $this->classes;
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
