<?php
/**
 * @file    elements/url.php
 * @brief   url input element
 **/

namespace depage\htmlform\elements;

/**
 * @brief HTML url input type
 **/
class url extends text {
    // {{{ setDefaults()
    /**
     * @brief   collects initial values across subclasses
     *
     * @return  void
     **/
    protected function setDefaults() {
        parent::setDefaults();

        $this->defaults['errorMessage'] = 'Please enter a valid URL!';
    }
    // }}}
}
