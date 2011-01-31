<?php

namespace depage\htmlform\exceptions;

class duplicateElementNameException extends containerException {
    public function __construct() {
        parent::__construct("Element name already in use.");
    }
}
