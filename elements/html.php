<?php 
/**
 * @file    html.php
 * @brief   html element
 **/

namespace depage\htmlform\elements;

use depage\htmlform\exceptions;

/**
 * @brief Can be used to add custom HTML between rendered HTML elements.
 **/
class html {
    // {{{ variables
    /**
     * @brief HTML code to be printed
     **/
    private $htmlString;
    // }}}

    // {{{ __construct()
    /**
     * @brief html class constructor
     *
     * @param $htmlString (string) HTML to be printed
     **/
    public function __construct($htmlString) {
        $this->htmlString = $htmlString;
    }
    // }}}

    // {{{ __toString()
    /**
     * @brief   Renders element to HTML.
     *
     * @return  $this->htmlString (string) HTML-rendered element
     **/
    public function __toString() {
        return (string) $this->htmlString;
    }
    // }}}
}
