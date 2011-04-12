<?php

namespace depage\htmlform\exceptions;

class unknownElementTypeException extends elementException {
    public function __construct() {
        parent::__construct("Unknown element type.");
    }
}
