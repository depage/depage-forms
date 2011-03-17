<?php
namespace depage\htmlform\validators;

class regExValidator extends validator {
    /**
     * Regular expression
     **/
    public $regEx = "//";

    public function __construct($log = null) {
        parent::__construct($log);

        $this->patternAttribute = " pattern=\"" . substr($this->regEx, 1,-1) . "\"";
    }

    public function validate($value) {
        return preg_match($this->regEx, $value) ? true : false;
    }
}
