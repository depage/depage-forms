<?php

namespace depage\htmlform\exceptions;

class containerNameNoStringException extends containerException {
    public function __construct() {
        parent::__construct("Container name needs to be a string.");
    }
}
