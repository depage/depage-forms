<?php
/**
 * @file    Validators/Url.php
 * @brief   url validator
 **/

namespace Depage\HtmlForm\Validators;

/**
 * @brief default validator for url input elements
 **/
class Url extends Validator
{
    // {{{ validate()
    /**
     * @brief   url validator
     *
     * @param  string $url        url to be validated
     * @param  array  $parameters validation parameters
     * @return bool   validation result
     **/
    public function validate($url, $parameters = array())
    {
        return (bool) filter_var(idn_to_ascii($url), FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED + FILTER_FLAG_HOST_REQUIRED);
    }
    // }}}
}
