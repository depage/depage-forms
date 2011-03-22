<?php
namespace depage\htmlform\validators;

class validator {
    /**
     * HTML pattern Attrbute.
     **/
    protected $patternAttribute = "";
    /**
     * Log object
     **/
    protected $log;

    public function __construct($log = null) {
        $this->log = $log;
    }

    public static function factory($argument, $log = null) {
        if (($argument[0] === '/') && ($argument[strlen($argument)-1] ==='/')) {
            $regExValidator = new regEx($log);
            $regExValidator->regEx = $argument;

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

    public function getPatternAttribute() {
        return $this->patternAttribute;
    }

    public function validate($value) {
        return true;
    }

    protected function log($argument, $type) {
        if (is_callable(array($this->log, 'log'))) {
            $this->log->log($argument, $type);
        } else {
            error_log($argument);
        }
    }
}
