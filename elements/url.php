<?php

namespace depage\htmlform\elements;

/**
 * HTML url input type.
 **/
class url extends text {
    public function __construct($name, $parameters, $formName) {
        parent::__construct($name, $parameters, $formName);

        $this->errorMessage = (isset($parameters['errormessage'])) ? $parameters['errormessage'] : 'Please enter a valid URL!';
    }
}
