<?php
/**
 * @file    duplicateElementNameException.php
 * @brief   duplicate element name exception class
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace depage\htmlform\exceptions;

/**
 * @brief thrown when there are duplicate element names
 *
 * Element names are also used for identification, so they have to be unique.
 **/
class duplicateElementNameException extends elementException {
}
