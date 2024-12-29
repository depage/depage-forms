<?php

/**
 * @file    placeholder.php
 * @brief   Adds a Placeholder for a form element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Ben Wallis <benedict_wallis@yahoo.co.uk>
 **/

namespace Depage\HtmlForm\Elements;

use Depage\HtmlForm\Abstracts;

/**
 * @brief Adds a placeholder for a form element
 *
 * Usage
 * -----
 *
 * @code
    <?php
        $form = new Depage\HtmlForm\HtmlForm('myform');

        // add a hidden field
        $form->addPlaceholder('nonce');

        // process form
        $form->process();

        // Display the form.
        echo ($form);
    @endcode
 **/
class Placeholder extends Abstracts\Input
{
    // {{{ __toString()
    /**
     * @brief   Renders element to HTML.
     *
     * @return string HTML-rendered element
     **/
    public function __toString(): string
    {
        return "";
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
