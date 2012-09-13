<?php
/**
 * @file    placeholder.php
 * @brief   Adds a Placeholder for a form element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Ben Wallis <benedict_wallis@yahoo.co.uk>
 **/

namespace depage\htmlform\elements;

use depage\htmlform\abstracts;

/**
 * @brief Adds a placeholder for a form element
 *
 * @section usage
 *
 * @code
 * <?php
 *     $form = new depage\htmlform\htmlform('myform');
 *
 *     // add a hidden field
 *     $form->addPlaceholder('nonce');
 *
 *     // process form
 *     $form->process();
 *
 *     // Display the form.
 *     echo ($form);
 * ?>
 * @endcode
 **/
class placeholder extends abstracts\input {
    // {{{ __toString()
    /**
     * @brief   Renders element to HTML.
     *
     * @return  (string) HTML-rendered element
     **/
    public function __toString() {
        return "";
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
