<?php

namespace depage\htmlform\exceptions;

class unknownInputTypeException extends itemException {
    public function __construct() {
        parent::__construct("Unknown input type.");
    }
}
