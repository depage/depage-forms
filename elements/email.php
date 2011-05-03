<?php
/**
 * @file    email.php
 * @brief   email input element
 **/

namespace depage\htmlform\elements;

/**
 * @brief HTML email input type.
 **/
class email extends text {
    /**
     * @brief collects initial values across subclasses.
     **/
    protected function setDefaults() {
        parent::setDefaults();
        $this->defaults['errorMessage'] = 'Please enter a valid e-mail address!';
    }
}
