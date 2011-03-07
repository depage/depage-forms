<?php
namespace depage\htmlform\validators;

class telValidator{
    public function match($value) {
        $regEx = '/^[0-9,+,(), ,]{1,}(,[0-9]+){0,}$/';
        return preg_match($regEx, $value) ? true : false;
    }
}
