<?php 

require_once('container.php');

/**
 * The html class can be used to add custom HTML between rendered HTML 
 * elements.
 **/
class html {
    /**
     * string of HTML to be printed
     **/
    private $htmlString;
    
    /**
     * @param $htmlString string of HTML to be printed
     **/
    public function __construct($htmlString) {
        $this->htmlString = $htmlString;
    }

    /**
     * Renders element to HTML.
     *
     * @return string of HTML rendered element
     **/
    public function __toString() {
        return $this->htmlString;
    }
}
