<?php
/**
 * @file    duplicateElementNameException.php
 * @brief   duplicate element name exception class
 **/

namespace depage\htmlform\exceptions;

/**
 * @brief thrown when there are duplicate element names
 *
 * Element names are also used for identification, so they have to be unique.
 **/
class duplicateElementNameException extends elementException {
}
