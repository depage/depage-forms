<?php
namespace depage\htmlform\validators;

class validator {
    public static function factory($argument) {
        if ($argument[0] === '/') {
            return new regExValidator($argument);
        } else {
            $type = 'depage\\htmlform\\validators\\' . $argument . 'Validator';
            return new $type;
        }
    }
    public static function getPattern($validator) {
        if (($validator[0] === '/') && ($validator[strlen($validator)-1] ==='/')) {
            $pattern = substr($validator, 1,-1);
        } else {
            $pattern = false;
        }
        return $pattern;
    }
}
