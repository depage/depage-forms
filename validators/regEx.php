<?php
/**
 * @file    validators/regEx.php
 * @brief   regular expression validator
 **/
namespace depage\htmlform\validators;

/**
 * @brief customizable validator for input elements
 **/
class regEx extends validator {
    // {{{ variables
    /**
     * @brief regular expression
     **/
    protected $regEx = "//";
    // }}}

    // {{{ validate()
    /**
     * @brief   validates value with regular expression
     *
     * @param   $value      (string)    value to be validated
     * @param   $parameters (array)     validation parameters
     * @return              (bool)      validation result
     **/
    public function validate($value, $parameters = array()) {
        $match = (bool) preg_match($this->regEx, $value, $matchedSubstring);

        /**
         * To make the pattern-matching HTML5 compliant the regular expression
         * has to match the entire string. Since there is no preg_match flag
         * for that, we compare the value with the matched substring.
         **/
        $completeMatch = $match && ($value === $matchedSubstring[0]);

        if (preg_last_error() !== PREG_NO_ERROR) {
            /** @todo set error type **/
            $this->log("Regular expression warning: error code " . preg_last_error());
        }

        return $completeMatch;
    }
    // }}}

    // {{{ setRegEx()
    /**
     * @brief   sets the validators regular expression
     *
     * @param   $regEx (string) regular expression
     * @return  void
     **/
    public function setRegEx($regEx) {
        $this->regEx = $regEx;
    }
    // }}}
}
