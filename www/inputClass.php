<?php

abstract class inputClass {
    protected $type;
    protected $name;
    protected $attributes;

    public function __construct($type, $name, $attributes) {
        $this->type = $type;
        $this->name = $name;
        $this->attributes = $attributes;
    }
}
