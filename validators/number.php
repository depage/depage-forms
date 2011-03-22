<?php
namespace depage\htmlform\validators;

class number extends validator {
    public function validate($value, $min, $max) {
        return is_numeric($value)
            && (($value >= $min) || ($min === null))
            && (($value <= $max) || ($max === null));
    }
}
