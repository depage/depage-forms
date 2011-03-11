<?php

namespace depage\htmlform\elements;

/**
 * HTML tel input type.
 **/
class tel extends text {
    public function __construct($name, $parameters, $formName) {
        parent::__construct($name, $parameters, $formName);

        $this->errorMessage = (isset($parameters['errorMessage'])) ? $parameters['errorMessage'] : 'Please enter a valid telephone number!';
    }
}
