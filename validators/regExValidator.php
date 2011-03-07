<?php
namespace depage\htmlform\validators;

class regExValidator {
    public function __construct($regEx) {
        $this->regEx = $regEx;
    }
    public function validate($value) {
        return preg_match($this->regEx, $value) ? true : false;
    }
}
