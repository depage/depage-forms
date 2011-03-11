<?php

namespace depage\htmlform\elements;

use depage\htmlform\abstracts;

/**
 * HTML tel input type.
 **/
class tel extends abstracts\textClass {
    public function __construct($name, $parameters, $formName) {
        parent::__construct($name, $parameters, $formName);

        $this->errorMessage = (isset($parameters['errorMessage'])) ? $parameters['errorMessage'] : 'Please enter a valid telephone number!';
    }
}
