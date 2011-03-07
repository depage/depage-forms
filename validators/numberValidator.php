<?php
namespace depage\htmlform\validators;

class numberValidator {
    public function validate($value, $min, $max) {
        return (($value >= $min) || ($min === null))
            && (($value <= $max) || ($max === null));
    }
}
