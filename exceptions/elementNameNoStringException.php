<?php

namespace depage\htmlform\exceptions;

class elementNameNoStringException extends elementException {
    public function __construct() {
        parent::__construct("Element name needs to be a string.");
    }
}
