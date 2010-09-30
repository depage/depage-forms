<?php

abstract class inputClass {
    protected $type;
    protected $name;
    protected $label;
    protected $required;
    protected $formName;
    protected $value;

    public function __construct($type, $name, $parameters, $formName) {
        $this->type = $type;
        $this->name = $name;
        $this->formName = $formName;
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
}
