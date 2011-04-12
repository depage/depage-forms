<?php
namespace depage\htmlform\validators;

/**
 * Custom validator for input elements.
 **/
class regEx extends validator {
    /**
     * Validators' regular expression
     **/
    protected $regEx = "//";

    /**
     * Overrides validator::validate; regular expression validation.
     *
     * @param   $value      (string)    value to be validated
     * @param   $parameters (array)     validation parameters
     * @return              (bool)      validation result
     **/
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
