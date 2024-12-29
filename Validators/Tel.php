<?php

/**
 * @file    Validators/Tel.php
 * @brief   telephone number validator
 **/

namespace Depage\HtmlForm\Validators;

/**
 * @brief default validator for tel input elements
 **/
class Tel extends RegEx
{
    // {{{ __construct()
    /**
     * @brief   tel valdiator class constructor
     *
     * Sets regular expression for telephone number validation.
     *
     * @param  object $log error logger
     * @return void
     **/
    public function __construct($log = null)
    {
        parent::__construct($log);

        $this->regEx = '/^[0-9\/\(\)\+\-\. ]*$/';
    }
    // }}}

    // {{{ getPatternAttribute()
    /**
     * @brief   returns HTML5 pattern attribute
     *
     * Overrides parent to return empty string (tel input element doesn't
     * require pattern attribute for HTML5 validation).
     *
     * @return string empty attribute string
     **/
    public function getPatternAttribute()
    {
        return '';
    }
    // }}}
}
