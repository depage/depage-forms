<?php

namespace depage\htmlform\exceptions;

class inputParametersNoArrayException extends inputException {
    public function __construct() {
        parent::__construct("Input parameters need to be in an array.");
    }
}
