<?php
/**
 * @file    validators/email.php
 * @brief   email validator
 **/

namespace depage\htmlform\validators;

/**
 * @brief default validator for email input elements
 **/
class email extends validator
{
    // {{{ validate()
    /**
     * @brief   email validation
     *
     * @param  string $email      email to be validated
     * @param  array  $parameters validation parameters
     * @return bool   validation result
     **/
    public function validate($email, $parameters = array())
    {
        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    // }}}
}
