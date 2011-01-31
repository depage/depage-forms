<?php

namespace depage\htmlform\exceptions;

class wrongCheckedParameterCombinationException extends inputException {
    public function __construct() {
        parent::__construct("Wrong parameter - option list combination.");
    }
}
