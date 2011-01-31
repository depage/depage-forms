<?php

namespace depage\htmlform\exceptions;

class inputNameNoStringException extends inputException {
    public function __construct() {
        parent::__construct("Input name needs to be a string.");
    }
}
