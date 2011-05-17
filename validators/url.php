<?php
/**
 * @file    validators/url.php
 * @brief   url validator
 **/

namespace depage\htmlform\validators;

/**
 * @brief default validator for url input elements
 **/
class url extends validator {
    // {{{ validate()
    /**
     * @brief   url validator
     *
     * @param   $url        (string)    url to be validated
     * @param   $parameters (array)     validation parameters
     * @return              (bool)      validation result
     **/
    public function validate($url, $parameters = array()) {
        return (bool) filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED + FILTER_FLAG_HOST_REQUIRED);
    }
    // }}}
}
