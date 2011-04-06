<?php

namespace depage\htmlform\elements;

/**
 * HTML email input type.
 **/
class email extends text {
    /**
     * collects initial values across subclasses.
     **/
    protected function setDefaults() {
        parent::setDefaults();
        $this->defaults['errorMessage'] = 'Please enter a valid e-mail address!';
    }
}
