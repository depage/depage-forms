<?php
namespace depage\htmlform\validators;

/**
 * Validator for number input elements.
 **/
class number extends validator {
    /**
     * Overrides validator::validate; number validation.
     *
     * @param   $value      (int)   value to be validated
     * @param   $parameters (array) validation parameters
     * @return              (bool)  validation result
     **/
    public function validate($value, $parameters = array()) {
        $min = isset($parameters['min']) ? $parameters['min'] : null;
        $max = isset($parameters['max']) ? $parameters['max'] : null;

        return is_numeric($value)
            && (($value >= $min) || ($min === null))
            && (($value <= $max) || ($max === null));
    }
}
