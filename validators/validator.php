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
}
