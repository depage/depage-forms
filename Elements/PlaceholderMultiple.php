<?php

/**
 * @file    placeholderMultiple.php
 * @brief   adds a placeholder for an array element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Ben Wallis <benedict_wallis@yahoo.co.uk>
 **/

namespace Depage\HtmlForm\Elements;

/**
 * @brief Placeholder for form multiple form values.
 *
 * Usage
 * -----
 *
 * @code
    <?php
        $form = new Depage\HtmlForm\HtmlForm('myform');

        // add a hidden field
        $form->addPlaceholderMutliple('nonce');

    @endcode
 **/
class PlaceholderMultiple extends Placeholder
{
    // {{{ typeCastValue()
    /**
     * @brief   Casts to array.
     *
     * @return array empty array
     **/
    public function typeCastValue(): array
    {
        return [];
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
