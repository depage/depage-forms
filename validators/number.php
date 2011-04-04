<?php
namespace depage\htmlform\validators;

class number extends validator {
    public function validate($value, $parameters = array()) {
        $min = isset($parameters['min']) ? $parameters['min'] : null;
        $max = isset($parameters['max']) ? $parameters['max'] : null;

        return is_numeric($value)
            && (($value >= $min) || ($min === null))
            && (($value <= $max) || ($max === null));
    }
}
