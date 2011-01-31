<?php

namespace depage\htmlform\exceptions;

class htmlNoStringException extends Exception {
    public function __construct() {
        parent::__construct("HTML needs to be parsed as type string.");
    }
}
