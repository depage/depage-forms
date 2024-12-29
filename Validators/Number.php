<?php

/**
 * @file    Validators/Number.php
 * @brief   number validator
 **/

namespace Depage\HtmlForm\Validators;

/**
 * @brief default validator for number input elements
 **/
class Number extends Validator
{
    // {{{ validate()
    /**
     * @brief   number validation
     *
     * @param  int   $value      value to be validated
     * @param  array $parameters validation parameters
     * @return bool  validation result
     **/
    public function validate($value, $parameters = [])
    {
        $min = isset($parameters['min']) ? $parameters['min'] : null;
        $max = isset($parameters['max']) ? $parameters['max'] : null;

        return is_numeric($value)
            && (($value >= $min) || ($min === null))
            && (($value <= $max) || ($max === null));
    }
    // }}}
}
