<?php
namespace depage\htmlform\validators;

class email extends regEx {
    public function __construct($log = null) {
        parent::__construct($log);

        $this->regEx ='/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/';
        $this->patternAttribute = "";
    }
}
