<?php

/**
 * @file    Validators/Validator.php
 * @brief   basic validator
 **/

namespace Depage\HtmlForm\Validators;

/**
 * @brief parent validator class
 *
 * Basic validator. Contaіns validator factory.
 **/
class Validator
{
    // {{{ variables
    /**
     * @brief log object
     **/
    protected $log;
    // }}}

    // {{{ __construct()
    /**
     * @brief   validator constructor
     *
     * Attaches error logging object to validator.
     *
     * @param  object $log error logging object
     * @return void
     **/
    public function __construct($log = null)
    {
        $this->log = $log;
    }
    // }}}

    // {{{ factory()
    /**
     * @brief   valdiator object factory
     *
     * Static validator object factory. Picks validator type depending on
     * $argument.
     *
     * @param  string $argument validator type or regular expression or closure
     * @param  object $log      error logging object
     * @return object validator object
     **/
    public static function factory($argument, $log = null)
    {
        if (!is_string($argument) && is_callable($argument, false)) {
            $closureValidator = new Closure($log);
            $closureValidator->setFunc($argument);

            return $closureValidator;
        } elseif (($argument[0] === '/') && ($argument[strlen($argument) - 1] === '/')) {
            $regExValidator = new RegEx($log);
            $regExValidator->setRegEx($argument);

            return $regExValidator;
        } else {
            $type = 'Depage\\HtmlForm\\Validators\\' . $argument;

            if (class_exists($type)) {
                return new $type($log);
            } else {
                return new Validator($log);
            }
        }
    }
    // }}}

    // {{{ validate()
    /**
     * @brief   default validator.
     *
     * Everything is valid. To be overriden in specific validator objects.
     *
     * @param  mixed $value      value to be validated
     * @param  array $parameters validation parameters
     * @return bool  validation result
     **/
    public function validate($value, $parameters = [])
    {
        return true;
    }
    // }}}

    // {{{ log()
    /**
     * @brief   error logging method
     *
     * @param  string $argument error message
     * @param  string $type     error type
     * @return void
     **/
    protected function log($argument, $type)
    {
        if (is_callable([$this->log, 'log'])) {
            $this->log->log($argument, $type);
        } else {
            error_log($argument);
        }
    }
    // }}}

    // {{{ getPatternAttribute()
    /**
     * @brief   returns validators' regular expression as HTML5 pattern attribute
     *
     * @return string HTML pattern attribute
     **/
    public function getPatternAttribute()
    {
        if (isset($this->regEx)) {
            return ' pattern="' . htmlspecialchars(substr($this->regEx, 1, -1), ENT_QUOTES) . '"';
        }
    }
    // }}}

    // isValid() {{{
    /**
     * Returns a bool value indicating whether or not the input passes
     * the given element's validation criteria.
     *
     * @return bool isValid
     */
    public static function isValid($input)
    {
        $el = \get_called_class();
        $el = new $el();

        return $el->validate($input);
    }
    // }}}
}
