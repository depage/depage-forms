<?php
namespace depage\htmlform\validators;

class email extends validator {
    public function validate($email, $parameters = array()) {
        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
