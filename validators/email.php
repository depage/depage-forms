<?php
namespace depage\htmlform\validators;

/**
 * Validator for email input elements.
 **/
class email extends validator {
    /**
     * Overrides validator::validate; email validation.
     *
     * @param   $email      (string)    email to be validated
     * @param   $parameters (array)     validation parameters
     * @return              (bool)      validation result
     **/
    public function validate($email, $parameters = array()) {
        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
