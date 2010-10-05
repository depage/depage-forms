<?php

abstract class inputClass {
    protected $type;
    protected $name;
    protected $label;
    protected $required;
    protected $formName;
    protected $value;
    protected $valid;

    public function __construct($type, $name, $parameters, $formName) {
        $this->_checkInputName($name);
        $this->_checkInputParameters($parameters);

        $this->type = $type;
        $this->name = $name;
        $this->formName = $formName; // @todo check?
        $this->label = (isset($parameters['label'])) ? $parameters['label'] : '';
        $this->required = (isset($parameters['required'])) ? $parameters['required'] : '';
        $this->value = (isset($parameters['value'])) ? $parameters['value'] : '';
    }

    public function getName() {
        return $this->name;
    }

    public function __toString() {
        $renderedUniqueId = ' id="' . $this->formName . '-' . $this->name . '"';
        return '<p' . $renderedUniqueId . '>' . $this->render() . '</p>';
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
