<?php

namespace depage\htmlform\exceptions;

class invalidContainerNameException extends containerException {
    public function __construct() {
        parent::__construct("Invalid container name.");
    }
}
