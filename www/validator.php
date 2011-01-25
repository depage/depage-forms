<?php

class validator {
    private $RegEx;
    private $wildcard = false;

    public function __construct($argument = '') {
        // if argument string starts with a '/' it's used as a regular expression
        if ((isset($argument[0])) && ($argument[0] === '/')) {
            $this->RegEx = $argument;
        } else {
            switch($argument) {
                case 'email':
                    $this->RegEx = '/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/';
                    break;
                case 'url':
                    $this->RegEx = '/(((ht|f)tp(s?):\/\/)|(www\.[^ \[\]\(\)\n\r\t]+)|(([012]?[0-9]{1,2}\.){3}[012]?[0-9]{1,2})\/)([^ \[\]\(\),;&quot;\'&lt;&gt;\n\r\t]+)([^\. \[\]\(\),;&quot;\'&lt;&gt;\n\r\t])|(([012]?[0-9]{1,2}\.){3}[012]?[0-9]{1,2})/';
                    break;
                case 'tel':
                    $this->RegEx = '/^[0-9,+,(), ,]{1,}(,[0-9]+){0,}$/';
                    break;
                default: 
                    $this->wildcard = true;
            }
        }
    }
    
    public function match($value) {
        if ($this->wildcard) {
            return true;
        } else {
            return preg_match($this->RegEx, $value) ? true : false;
        }
    }
}
