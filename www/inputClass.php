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
        $this->label = $parameters['label'];
        $this->required = $parameters['required'];
        $this->value = $parameters['value'];
    }

    public function getName() {
        return $this->name;
    }

    public function __toString() {
        $renderedUniqueId = ' id="' . $this->formName . '-' . $this->name . '"';
        return '<p' . $renderedUniqueId . '>' . $this->render() . '</p>';
    }

    private function _checkInputName($name) {
        if (!is_string($name)) {
            throw new Exception('Input name needs to be a string: ' . $name);
        }
        if (trim($name) === '') {
            throw new Exception('Invalid input name');
        }
    }

    private function _checkInputParameters($parameters) {
        if ((isset($parameters)) && (!is_array($parameters))) {
            throw new Exception('Input parameters need to be in an array');
        }
    }
}
