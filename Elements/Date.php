<?php

/**
 * @file    date.php
 * @brief   date input element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace Depage\HtmlForm\Elements;

/**
 * @brief   HTML date input type.
 *
 * @todo    dummy - no validator implemented yet
 **/
class Date extends Text
{
    // {{{ setDefaults()
    /**
     * @brief   sets defaults for date input
     *
     * @return void
     **/
    protected function setDefaults(): void
    {
        parent::setDefaults();

        $this->defaults['placeholder']  = _("YYYY-MM-DD");
        $this->defaults['validator']  = "/\d{4}-\d{2}-\d{2}/";
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
