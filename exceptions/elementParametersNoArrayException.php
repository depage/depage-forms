<?php

namespace depage\htmlform\exceptions;

class elementParametersNoArrayException extends elementException {
    public function __construct() {
        parent::__construct("Element parameters need to be in an array.");
    }
}
