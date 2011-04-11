<?php

namespace depage\htmlform\exceptions;

class invalidElementNameException extends elementException {
    public function __construct() {
        parent::__construct("Invalid element name.");
    }
}
