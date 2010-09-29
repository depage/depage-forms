<?php

abstract class inputClass {
    protected $type;
    protected $name;
    protected $label;
    protected $required;

    public function __construct($type, $name, $parameters) {
        $this->type = $type;
        $this->name = $name;
        $this->label = $parameters['label'];
        $this->required = $parameters['required'];
    }

    public function getName() {
        return $this->name;
    }
}
