<?php

class validator {
    private $RegEx = '/.*/';

    public function __construct($argument) {
        if ((isset($argument[0])) && ($argument[0] === '/')) {
            $this->RegEx = $argument;
        } else {
            switch($argument) {
                case 'email':
                    $this->RegEx = '/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/';
                    break;
                case 'text':
                    break;
            }
        }
    }
    
    public function match($value) {
        return preg_match($this->RegEx, $value);
    }
}
