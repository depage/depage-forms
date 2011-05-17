<?php
/**
 * @file    validators/validator.php
 * @brief   basic validator
 **/

namespace depage\htmlform\validators;

/**
 * @brief parent validator class
 *
 * Basic validator. Contaіns validator factory.
 **/
 class validator {
    /**
     * @brief log object
     **/
    protected $log;

    /**
     * @brief   validator constructor
     *
     * Attaches error logging object to validator.
     *
     * @param   $log (object) error logging object
     * @return  void
     **/
    public function __construct($log = null) {
        $this->log = $log;
    }

    /**
     * @brief   valdiator object factory
     *
     * Static validator object factory. Picks validator type depending on
     * $argument.
     *
     * @param   $argument   (string) validator type or regular expression
     * @param   $log        (object) error logging object
     * @return              (object) validator object
     **/
    public static function factory($argument, $log = null) {
        if (($argument{0} === '/') && ($argument{strlen($argument)-1} ==='/')) {
            $regExValidator = new regEx($log);
            $regExValidator->setRegEx($argument);

            return $regExValidator;
        } else {
            $type = 'depage\\htmlform\\validators\\' . $argument;

            if (class_exists($type)) {
                return new $type($log);
            } else {
                return new validator($log);
            }
        }
    }

    /**
     * @brief   default validator.
     *
     * Everything is valid. To be overriden in specific validator objects.
     *
     * @param   $value      (mixed) value to be validated
     * @param   $parameters (array) validation parameters
     * @return              (bool)  validation result
     **/
    public function validate($value, $parameters = array()) {
        return true;
    }

    /**
     * @brief   error logging method
     *
     * @param   $argument (string) error message
     * @param   $type     (string) error type
     * @return  void
     **/
    protected function log($argument, $type) {
        if (is_callable(array($this->log, 'log'))) {
            $this->log->log($argument, $type);
        } else {
            error_log($argument);
        }
    }

    /**
     * @brief   returns validators' regular expression as HTML5 pattern attribute
     *
     * @return  (string) HTML pattern attribute
     **/
    public function getPatternAttribute() {
        if (isset($this->regEx)) {
            return ' pattern="' . htmlspecialchars(substr($this->regEx, 1,-1), ENT_QUOTES) . '"';
        }
    }
}
