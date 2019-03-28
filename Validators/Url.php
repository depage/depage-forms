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
        return (bool) filter_var($url, FILTER_VALIDATE_URL);
    }
    // }}}
}
