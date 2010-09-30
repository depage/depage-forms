<?php

abstract class inputClass {
    protected $type;
    protected $name;
    protected $label;
    protected $required;
    protected $formName;

    public function __construct($type, $name, $parameters, $formName) {
        $this->type = $type;
        $this->name = $name;
        $this->formName = $formName;
        $this->label = $parameters['label'];
        $this->required = $parameters['required'];
    }

    public function getName() {
        return $this->name;
    }

    public function __toString() {
        $uniqueID = $this->formName . '-' . $this->name;
        return '<p id="' . $uniqueID . '">' . $this->render() . '</p>';
    }
}
