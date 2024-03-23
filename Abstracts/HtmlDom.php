<?php
/**
 * @file    htmldom.php
 * @brief   DOM-class for html-content
 *
 * @author    Frank Hellenkamp [jonas@depage.net]
 */

namespace Depage\HtmlForm\Abstracts;

/**
 * @brief DOMDocument for html-content
 *
 * Serializable subclass of DOMDocument with helper methods especially
 * for html-content, and for removing up unwanted tags from html.
 */
class HtmlDom extends \DOMDocument implements \Serializable
{
    // {{{ variables
    /**
     * @brief Tags that are allowed inside of html
     **/
    protected $allowedTags = [
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
    ];

    /**
     * @brief allowedAttributes
     **/
    protected $allowedAttributes = [
        'class',
        'href',
        'target',
        'alt',
        'title',
        'data-dbid',
    ];
    // }}}

    // {{{ constructor()
    /**
     * @brief   htmldom class constructor
     *
     * @param string $version  xml-version
     * @param string $encoding encoding of xml document
     *
     * @return Depage::HtmlForm::Abstracts::HtmlDom htmlDOM
     **/
    public function __construct($version = null, $encoding = null)
    {
        parent::__construct($version, $encoding);
    }
    // }}}
    // {{{ serialize()
    /**
     * @brief   serializes htmldom into string
     *
     * @return string xml-content saved by saveXML()
     **/
    public function serialize()
    {
        $s = $this->saveXML();

        return $s;
    }
    // }}}
    // {{{ unserialize()
    /**
     * @brief   unserializes htmldom-objects
     *
     * @param   string  $serialized php-serialized xml string
     *
     * @return void
     **/
    public function unserialize($serialized)
    {
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
     * @param string $html html to parse
     *
     * @return boolean true on success, false on error
     **/
    public function loadHTML($html, $options = null)
    {
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

        foreach ($nodelist as $node) {
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
     * @param array $allowedTags array of tags that are alowed inside of html
     *
     * @return void
     **/
    public function cleanHTML($allowedTags = null, $allowedAttributes = null)
    {
        $xpath = new \DOMXPath($this);

        if (is_null($allowedTags)) {
            $allowedTags = $this->allowedTags;
        }
        if (is_null($allowedAttributes)) {
            $allowedAttributes = $this->allowedAttributes;
        }

        // {{{ split tags and classnames
        $tags = [];
        $classByTag = [];

        foreach ($allowedTags as $t) {
            preg_match("/([a-zA-Z0-9]*)(\.(.*))?/", $t, $matches);

            $tag = $matches[1] ?? "";
            $class = $matches[3] ?? "";
            $tags[$tag] = true;
            if (!isset($classByTag[$tag])) {
                $classByTag[$tag] = [];
            }
            if (!empty($class)) {
                $classByTag[$tag][] = $class;
            } else {
                $classByTag[$tag][] = "";
            }
        }
        // }}}

        // {{{ remove all nodes with tagnames that are not allowed
        $nodelist = $xpath->query("//body//*");

        for ($i = $nodelist->length - 1; $i >= 0; $i--) {
            $node = $nodelist->item($i);

            if (!isset($tags[$node->nodeName])) {
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
            } else {
                // test for allowed attributes
                for ($j = $node->attributes->length - 1; $j >= 0; $j--) {
                    $attr = $node->attributes->item($j);

                    // remove attributes that are not in allowedAttributes
                    if (!in_array($attr->name, $allowedAttributes)) {
                        $node->removeAttribute($attr->name);
                    }
                }

                // remove invalid classnames
                if ($node->getAttribute("class") != "") {
                    $attr = implode(" ", array_intersect(
                        explode(" ", $node->getAttribute("class")),
                        $classByTag[$node->nodeName]
                    ));
                    if (empty($attr)) {
                        $node->removeAttribute("class");
                    } else {
                        $node->setAttribute("class", $attr);
                    }
                }
            }
        }
        // }}}
        // @todo check to use br or nbsp
        // {{{ add br to empty paragraphs to keep them
        $nodelist = $xpath->query("//p[. = '' and count(br) = 0]");

        foreach ($nodelist as $node) {
            $node->appendChild($this->createElement("br"));
        }
        // }}}
        // {{{ make sure li-nodes are always inside ul, ol or menu
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
        // {{{ remove empty inline element
        $nodelist = $xpath->query("//b[not(node())] | //i[not(node())] | //strong[not(node())] | //span[not(node())] | //a[not(node())]");

        for ($i = $nodelist->length - 1; $i >= 0; $i--) {
            $node = $nodelist->item($i);

            $node->parentNode->removeChild($node);
        }
        // }}}
        // {{{ clean nodes if only one paragraph with a br to leave only empty string in result body
        $nodes = $this->getBodyNodes();
        if ($nodes->length == 1) {
            $node = $nodes->item(0);
            if ($node->nodeName == "p" && $node->childNodes->length == 1 && $node->childNodes->item(0)->nodeName == "br") {
                $node->parentNode->removeChild($node);
            }
        }
        // }}}
    }
    // }}}
    // {{{ cutToMaxlength()
    /**
     * @brief cutToMaxlength
     *
     * @param mixed $max
     * @return void
     **/
    public function cutToMaxlength($max)
    {
        $charsToRemove = mb_strlen($this->documentElement->textContent) - $max;

        if ($charsToRemove <= 0) {
            return;
        }

        $xpath = new \DOMXPath($this);
        $textNodes = $xpath->query("//text()");
        $i = $textNodes->length - 1;
        while ($charsToRemove > 0 && $i >= 0) {
            $n = $textNodes->item($i);
            $len = mb_strlen($n->textContent);
            $parent = $n->parentNode;

            if ($len <= $charsToRemove) {
                $parent->removeChild($n);
                $charsToRemove -= $len;
            } else {
                $restNode = $n->splitText($len - $charsToRemove);
                $parent->removeChild($restNode);
                $charsToRemove = 0;
            }

            // remove empty nodes
            if (mb_strlen($parent->textContent) == 0) {
                $parent->parentNode->removeChild($parent);
            }

            $i--;
        }
    }
    // }}}
    // {{{ __toString()
    /**
     * @brief __toString
     *
     * @param mixed
     * @return void
     **/
    public function __toString()
    {
        $html = "";
        foreach ($this->documentElement->childNodes as $node) {
            $html .= $this->saveXML($node) . "\n";
        }

        return $html;
    }
    // }}}
    // {{{ getBodyNodes()
    /**
     * @brief   gets a nodelist with all nodes inside the body
     *
     * @return nodelist nodes from body
     **/
    public function getBodyNodes()
    {
        $xpath = new \DOMXPath($this);
        $nodelist = $xpath->query("//body/*");

        return $nodelist;
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
