<?php

/**
 * @file    unknownElementTypeException.php
 * @brief   unknown element type exception class
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace Depage\HtmlForm\Exceptions;

/**
 * @brief thrown when attemting to instantiate an inexistent element class
 **/
class UnknownElementTypeException extends ElementException
{
    public function __construct($type)
    {
        parent::__construct("Unknown element type '$type'.");
    }
}
