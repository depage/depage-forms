<?php
namespace depage\htmlform\validators;

/**
 * Validator for tel input elements.
 **/
class tel extends regEx {
    /**
     * Setting regular expression for telephone number validation.
     **/
    public function __construct($log = null) {
        parent::__construct($log);

        $this->regEx = '/^[0-9,+,(), ,]+(,[0-9]+)*$/';
    }

    /**
     * Overrides parent to return empty string (tel input element doesn't
     * require pattern attribute for HTML5 validation).
     *
     * @return (string) empty attribute string
     **/
    public function getPatternAttribute() {
        return '';
    }
}
