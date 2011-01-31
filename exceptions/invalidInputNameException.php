<?php

namespace depage\htmlform\exceptions;

class invalidInputNameException extends inputException {
    public function __construct() {
        parent::__construct("Invalid input name.");
    }
}
