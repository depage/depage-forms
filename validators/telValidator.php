<?php
namespace depage\htmlform\validators;

class telValidator extends regExValidator {
    public function __construct($log = null) {
        parent::__construct($log);

        $this->regEx = '/^[0-9,+,(), ,]{1,}(,[0-9]+){0,}$/';
        $this->patternAttribute = "";
    }
}
