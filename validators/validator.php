<?php
namespace depage\htmlform\validators;

class validator {
    /**
     * HTML pattern Attrbute.
     **/
    protected $patternAttribute = "";

    public static function factory($argument) {
        if (($argument[0] === '/') && ($argument[strlen($argument)-1] ==='/')) {
            return new regExValidator($argument);
        } else {
            $type = 'depage\\htmlform\\validators\\' . $argument . 'Validator';

            if (class_exists($type)) {
                return new $type;
            } else {
                return new validator;
            }
        }
    }

    public function getPatternAttribute() {
        return $this->patternAttribute;
    }

    public function validate($value) {
        return true;
    }
}
