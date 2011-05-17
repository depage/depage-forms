<?php
/**
 * @file    unknownElementTypeException.php
 * @brief   unknown element type exception class
 **/

namespace depage\htmlform\exceptions;

/**
 * @brief thrown when attemting to instantiate an inexistent element class
 **/
class unknownElementTypeException extends elementException {
    public function __construct() {
        parent::__construct("Unknown element type.");
    }
}
