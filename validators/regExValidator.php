<?php
namespace depage\htmlform\validators;

class regExValidator extends validator {
    public function __construct($regEx) {
        $this->regEx = $regEx;
        $this->patternAttribute = " pattern=\"" . substr($this->regEx, 1,-1) . "\"";
    }
    public function validate($value) {
        return preg_match($this->regEx, $value) ? true : false;
    }
}
