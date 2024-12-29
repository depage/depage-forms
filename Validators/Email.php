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
    public function validate($email, $parameters = [])
    {
        $valid = (bool) filter_var($email, FILTER_VALIDATE_EMAIL);

        if ($valid && $parameters['checkDns']) {
            list($user, $domain) = explode('@', $email);

            $domain = idn_to_ascii($domain, IDNA_NONTRANSITIONAL_TO_ASCII, INTL_IDNA_VARIANT_UTS46);

            $valid = checkdnsrr(idn_to_ascii($domain . "."), 'MX');
        }

        return $valid;
    }
    // }}}
}
