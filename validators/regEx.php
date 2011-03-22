<?php
namespace depage\htmlform\validators;

class regEx extends validator {
    /**
     * Regular expression
     **/
    public $regEx = "//";

    public function __construct($log = null) {
        parent::__construct($log);

        $this->patternAttribute = " pattern=\"" . substr($this->regEx, 1,-1) . "\"";
    }

    public function validate($value) {
        $match = preg_match($this->regEx, $value) ? true : false;

        if (preg_last_error() !== PREG_NO_ERROR) {
            $this->log("Regular expression warning: error code " . preg_last_error());
        }

        return $match;
    }
}
