<?php

namespace depage\htmlform\abstracts;

class htmldom extends \DOMDocument implements \Serializable {
    // {{{ variables
    /**
     * @brief Tags that are allowed inside of html
     **/
    protected $allowedTags = array(
        "p",
        "ul",
        "ol",
        "li",

        "b",
        "strong",
        "i",
        "em",
    );
    // }}}

    // {{{ constructor()
    /**
     * @brief   
     *
     * @param   (DOMDocument) htmlDOM
     *
     * @return  (\depage\htmlform\abstracts\SerDOMDocument) htmlDOM
     **/
    public function __construct($version = null, $encoding = null) {
        parent::__construct($version, $encoding);
    }
    // }}}
    // {{{ serialize()
    /**
     * @brief   
     *
     * @param   (DOMDocument) htmlDOM
     *
     * @return  (\depage\htmlform\abstracts\SerDOMDocument) htmlDOM
     **/
    public function serialize(){
        $s = $this->saveXML();
        return $s;
    }
    // }}}
    // {{{ unserialize()
    /**
     * @brief   
     *
     * @param   (DOMDocument) htmlDOM
     *
     * @return  (\depage\htmlform\abstracts\SerDOMDocument) htmlDOM
     **/
    public function unserialize($serialized) {
        $this->loadXML($serialized);
    }
    // }}}
    // {{{ loadHTML()
    /**
     * @brief   
     *
     * @param   (DOMDocument) htmlDOM
     *
     * @return  (\depage\htmlform\abstracts\SerDOMDocument) htmlDOM
     **/
    public function loadHTML($html) {
        $tmpDOM = new \DOMDocument();

        $encoding = mb_http_input();
        if ($encoding == '') {
            $encoding = "utf-8";
        }

        // @todo take original content-type if available
        $success = $tmpDOM->loadHTML("<meta http-equiv=\"content-type\" content=\"text/html; charset=$encoding\">$html");

        $xpath = new \DOMXPath($tmpDOM);
        $nodelist = $xpath->query("//body/*");

        $this->loadXML("<body></body>");
        if ($tmpDOM->encoding != '') {
            $this->encoding = $tmpDOM->encoding;
        }
        $rootnode = $this->documentElement;

        foreach($nodelist as $node) {
            // copy all nodes inside the body tag to target document
            $newnode = $this->importNode($node, true);
            $rootnode->appendChild($newnode);
        }

        return $success;
    }
    // }}}
    // {{{ cleanHTML()
    /**
     * @brief   cleans up a htmlDOM 
     *
     * cleans up a htmlDOM and removed all tags and attributes 
     * that are not allowed
     *
     * @param   (array) $allowedTags array of tags that are alowed inside of html
     *
     * @return  (void)
     **/
    public function cleanHTML($allowedTags = null) {
        if (is_null($allowedTags)) {
            $allowedTags = $this->allowedTags;
        }
        // clean up
        
        // add &nbsp; to empty paragraphs to keep them
    }
    // }}}
}

/* vim:set ft=php fenc=UTF-8 sw=4 sts=4 fdm=marker et : */
