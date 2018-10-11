<?php
/**
 * @file    Validators/Email.php
 * @brief   email validator
 **/

namespace Depage\HtmlForm\Validators;

/**
 * @brief default validator for email input elements
 **/
class Email extends Validator
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
        $valid = (bool) filter_var($email, FILTER_VALIDATE_EMAIL);

        if ($valid && $parameters['checkDns']) {
            list($user, $domain) = explode('@', $email);

            $valid = checkdnsrr(idn_to_ascii($domain . "."), 'MX');
        }

        return $valid;
    }
    // }}}
}
