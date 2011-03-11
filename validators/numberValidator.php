<?php
namespace depage\htmlform\validators;

class numberValidator extends validator {
    public function validate($value, $min, $max) {
        return (($value >= $min) || ($min === null))
            && (($value <= $max) || ($max === null));
    }
}
