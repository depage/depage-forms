<?php
namespace depage\htmlform\validators;

class email extends validator {
    public function validate($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
