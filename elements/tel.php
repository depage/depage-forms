<?php

namespace depage\htmlform\elements;

/**
 * HTML tel input type.
 **/
class tel extends text {
    /**
     * collects initial values across subclasses.
     **/
    protected function setDefaults() {
        parent::setDefaults();

        $this->defaults['errorMessage'] = 'Please enter a valid telephone number!';
    }
}
