<?php
namespace depage\htmlform\validators;

class emailValidator {
    public function validate($value) {
        return preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/', $value) ? true : false;
    }
}
