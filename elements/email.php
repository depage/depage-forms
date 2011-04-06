<?php

namespace depage\htmlform\elements;

/**
 * HTML email input type.
 **/
class email extends text {
    public function __construct($name, $parameters, $form) {
        parent::__construct($name, $parameters, $form);

        $this->errorMessage = (isset($parameters['errormessage'])) ? $parameters['errormessage'] : 'Please enter a valid e-mail address!';
    }
}
