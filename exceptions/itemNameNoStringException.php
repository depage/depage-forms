<?php

namespace depage\htmlform\exceptions;

class itemNameNoStringException extends itemException {
    public function __construct() {
        parent::__construct("Item name needs to be a string.");
    }
}
