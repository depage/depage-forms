<?php

namespace depage\htmlform\elements;

/**
 * HTML tel input type.
 **/
class tel extends text {
    public function __construct($name, $parameters, $form) {
        parent::__construct($name, $parameters, $form);

        $this->errorMessage = (isset($parameters['errormessage'])) ? $parameters['errormessage'] : 'Please enter a valid telephone number!';
    }
}
