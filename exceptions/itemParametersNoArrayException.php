<?php

namespace depage\htmlform\exceptions;

class itemParametersNoArrayException extends itemException {
    public function __construct() {
        parent::__construct("Item parameters need to be in an array.");
    }
}
