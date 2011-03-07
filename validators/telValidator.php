<?php
namespace depage\htmlform\validators;

class telValidator extends regExValidator {
    public function __construct() {
        $regEx = '/^[0-9,+,(), ,]{1,}(,[0-9]+){0,}$/';
        parent::__construct($regEx);
    }
}
