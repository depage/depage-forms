<?php
/**
 * @file    validators/closure.php
 * @brief   closure validator
 **/
namespace depage\htmlform\validators;

/**
 * @brief customizable validator for input elements
 **/
class closure extends validator {
    // {{{ variables
    /**
     * @brief function to call
     **/
    protected $func;
    // }}}

    // {{{ validate()
    /**
     * @brief   validates value with a callable function/closure
     *
     * @param   string  $value      value to be validated
     * @param   array   $parameters validation parameters
     * @return  bool    validation result
     **/
    public function validate($value, $parameters = array()) {
        return call_user_func($this->func, $value, $parameters);
    }
    // }}}

    // {{{ setClosure()
    /**
     * @brief   sets the validators validator function
     *
     * @param   closure $func function
     * @return  void
     **/
    public function setFunc($func) {
        $this->func = $func;
    }
    // }}}
}

