<?php

namespace depage\htmlform\exceptions;

class invalidItemNameException extends itemException {
    public function __construct() {
        parent::__construct("Invalid item name.");
    }
}
