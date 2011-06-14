<?php
/**
 * @file    elements/tel.php
 * @brief   tel input element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace depage\htmlform\elements;

/**
 * @brief HTML tel input type.
 **/
class tel extends text {
    // {{{ setDefaults()
    /**
     * @brief   collects initial values across subclasses
     *
     * @return  void
     **/
    protected function setDefaults() {
        parent::setDefaults();

        $this->defaults['errorMessage'] = 'Please enter a valid telephone number!';
    }
    // }}}
}
