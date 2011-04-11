<?php
namespace depage\htmlform\validators;

class regEx extends validator {
    /**
     * Validators' regular expression
     **/
    protected $regEx = "//";

    public function __construct($log = null) {
        parent::__construct($log);
    }

    public function validate($value, $parameters = array()) {
        $match = (bool) preg_match($this->regEx, $value);

        if (preg_last_error() !== PREG_NO_ERROR) {
            // @todo set error type
            $this->log("Regular expression warning: error code " . preg_last_error(), null);
        }

        return $match;
    }

    public function setRegEx($regEx) {
        $this->regEx = $regEx;
    }
}
