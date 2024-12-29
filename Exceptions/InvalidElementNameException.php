<?php

/**
 * @file    invalidElementNameException.php
 * @brief   invalid element name exception class
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace Depage\HtmlForm\Exceptions;

/**
 * @brief thrown when element name is empty or contains invalid characters
 **/
class InvalidElementNameException extends ElementException {}
