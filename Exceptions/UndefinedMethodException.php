<?php

/**
 * @file    UndefinedMethodException.php
 * @brief   unknown element type exception class
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace Depage\HtmlForm\Exceptions;

/**
 * @brief thrown when attemting to instantiate an inexistent element class
 **/
class UndefinedMethodException extends ElementException
{
    public function __construct($name)
    {
        parent::__construct("Call to undefined method '$name'.");
    }
}
