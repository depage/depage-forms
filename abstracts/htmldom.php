<?php
/**
 * @file    htmldom.php
 * @brief   DOM-class for html-content
 *
 * @author    Frank Hellenkamp [jonas@depagecms.net]
 */

namespace depage\htmlform\abstracts;

/**
 * @brief DOMDocument for html-content
 *
 * Serializable subclass of DOMDocument with helper methods especially
 * for html-content, and for removing up unwanted tags from html.
 */
class htmldom extends \DOMDocument implements \Serializable {
    // {{{ variables
    /**
     * @brief Tags that are allowed inside of html
     **/
    protected $allowedTags = array(
        "p",
        "br",
        "h1",
        "h2",
        "ul",
        "ol",
        "li",

        "a",
        "b",
        "strong",
        "i",
        "em",
    );
    // }}}

    // {{{ constructor()
    /**
     * @brief   htmldom class constructor
     *
     * @param   $version (string)
     * @param   $encoding (string) 
     *
     * @return  (depage::htmlform::abstracts::htmldom) htmlDOM
     **/
    public function __construct($version = null, $encoding = null) {
        parent::__construct($version, $encoding);
    }
    // }}}
    // {{{ serialize()
    /**
     * @brief   serializes htmldom into string
     *
     * @return  (string) xml-content saved by saveXML()
     **/
    public function serialize(){
        $s = $this->saveXML();
        return $s;
    }
    // }}}
    // {{{ unserialize()
    /**
     * @brief   unserializes htmldom-objects
     *
     * @param   $serialized (string)
     *
     * @return  (void)
     **/
    public function unserialize($serialized) {
        $this->loadXML($serialized);
    }
    // }}}
    // {{{ loadHTML()
    /**
     * @brief   loads html from a htmls string
     *
     * Loads html into the htmldom. Invalid content is allowed. 
     * Only nodes inside of the body will be added to the dom.
     *
     * @param   $html (string) html to parse
     *
     * @return  (boolean) true on success, false on error
     **/
    public function loadHTML($html) {
        $tmpDOM = new \DOMDocument();

        $encoding = mb_http_input();
        if ($encoding == '') {
            $encoding = "utf-8";
        }

        // @todo take original content-type if available
        $success = @$tmpDOM->loadHTML("<meta http-equiv=\"content-type\" content=\"text/html; charset=$encoding\">$html");

        $xpath = new \DOMXPath($tmpDOM);
        $nodelist = $xpath->query("//body/*");

        $this->resolveExternals = true;
        $this->loadXML('<?xml version="1.0" encoding="utf-8"?>
            <!DOCTYPE html [ 
                <!ENTITY nbsp "&#160;">
            ]>
            <body></body>');
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
     * cleans up a htmlDOM and removes all tags and attributes 
     * that are not allowed.
     *
     * @param   $allowedTags (array) array of tags that are alowed inside of html
     *
     * @return  (void)
     **/
    public function cleanHTML($allowedTags = null) {
        $xpath = new \DOMXPath($this);

        if (is_null($allowedTags)) {
            $allowedTags = $this->allowedTags;
        }
        
        // {{{ remove all nodes with tagnames that are not allowed
        $nodelist = $xpath->query("//body//*");

        for ($i = $nodelist->length - 1; $i >= 0; $i--) {
            $node = $nodelist->item($i);

            if (!in_array($node->nodeName, $allowedTags)) {
                // move child nodes before element itself
                while ($node->firstChild != null) {
                    if ($node->parentNode->nodeName == "body" && $node->firstChild->nodeType  == XML_TEXT_NODE) {
                        // put text nodes into additional p when added directly to body
                        $paragraph = $node->parentNode->insertBefore($this->createElement("p"), $node);
                        $paragraph->appendChild($node->firstChild);
                    } else {
                        $node->parentNode->insertBefore($node->firstChild, $node);
                    }
                }
                
                // delete empty node
                $node->parentNode->removeChild($node);
            }
        }
        // }}}
        // {{{ add &nbsp; to empty paragraphs to keep them
        $nodelist = $xpath->query("//p[. = '']");

        foreach ($nodelist as $node) {
            $node->appendChild($this->createEntityReference("nbsp"));
        }
        // }}}
        // {{{ make sure li-nodes are alwas inside ul, ol or menu
        $nodelist = $xpath->query("//li");
        $parentNodes = array("ul", "ol", "menu");

        foreach ($nodelist as $node) {
            if (!in_array($node->parentNode->nodeName, $parentNodes)) {
                // find previous XML-element-node
                $previous = $node->previousSibling;
                while (!is_null($previous) && $previous->nodeType != XML_ELEMENT_NODE) {
                    $previous = $previous->previousSibling;
                }

                if (!is_null($previous) && in_array($previous->nodeName, $parentNodes)) {
                    // previous element is a list -> add node to it
                    $listNode->appendChild($node);
                } else {
                    // create a new ul-list and add element to it
                    $listNode = $node->parentNode->insertBefore($this->createElement("ul"), $node);
                    $listNode->appendChild($node);
                }
            }
        }
        // }}}
    }
    // }}}
    // {{{ getBodyNodes()
    /**
     * @brief   gets a nodelist with all nodes inside the body
     *
     * @return  (nodelist) nodes from body
     **/
    public function getBodyNodes() {
        $xpath = new \DOMXPath($this);
        $nodelist = $xpath->query("//body/*");

        return $nodelist;
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
