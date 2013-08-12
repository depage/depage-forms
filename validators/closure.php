<?php
/**
 * @file    validators/regEx.php
 * @brief   regular expression validator
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
     * @brief   validates value with regular expression
     *
     * @param   $value      (string)    value to be validated
     * @param   $parameters (array)     validation parameters
     * @return              (bool)      validation result
     **/
    public function validate($value, $parameters = array()) {
        return call_user_func($this->func, $value, $parameters);
    }
    // }}}

    // {{{ setClosure()
    /**
     * @brief   sets the validators validator function
     *
     * @param   $func (closure) function
     * @return  void
     **/
    public function setFunc($func) {
        $this->func = $func;
    }
    // }}}
}

