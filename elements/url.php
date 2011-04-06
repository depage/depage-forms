<?php

namespace depage\htmlform\elements;

/**
 * HTML url input type.
 **/
class url extends text {
    /**
     * collects initial values across subclasses.
     **/
    protected function setDefaults() {
        parent::setDefaults();

        $this->defaults['errorMessage'] = 'Please enter a valid URL!';
    }

}
