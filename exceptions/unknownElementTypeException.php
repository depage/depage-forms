<?php

namespace depage\htmlform\exceptions;

class unknownElementTypeException extends containerException {
    public function __construct() {
        parent::__construct("Unknown element type.");
    }
}
