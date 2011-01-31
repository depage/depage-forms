<?php

namespace depage\htmlform\exceptions;

class unknownInputTypeException extends inputException {
    public function __construct() {
        parent::__construct("Unknown input type.");
    }
}
