<?php
namespace depage\htmlform\validators;

class validator {
    /**
     * Log object
     **/
    protected $log;

    /**
     * Validator constructor. Attaches error logging object to validator.
     *
     * @param   $log (object) error logging object
     * @return  void
     **/
    public function __construct($log = null) {
        $this->log = $log;
    }

    /**
     * Static validator object factory. Picks validator type depending on
     * $argument.
     *
     * @param $argument (string) validator type or regular expression
     * @param $log      (object) error logging object
     * @return          (object) validator object
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
     * Default validator. Everything is valid. To be overriden in specific
     * validator objects.
     *
     * @param $value        (mixed) value to be validated
     * @param $parameters   (array) validation parameters
     * @return              (bool)  validation result
     **/
    public function validate($value, $parameters = array()) {
        return true;
    }

    /**
     * Error logging method.
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
     * Returns validators' regular expression as HTML5 pattern attribute.
     *
     * @return (string) HTML pattern attribute
     **/
    public function getPatternAttribute() {
        if (isset($this->regEx)) {
            return ' pattern="' . htmlentities(substr($this->regEx, 1,-1)) . '"';
        }
    }
}
