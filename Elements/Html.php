<?php

/**
 * @file    html.php
 * @brief   html element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 */

namespace Depage\HtmlForm\Elements;

use Depage\HtmlForm\Abstracts\HtmlDom as HtmlSanitizer;

/**
 * @brief Can be used to insert custom HTML between rendered HTML elements.
 *
 * Class for custom HTML code sections.
 *
 * Usage
 * -----
 *
 * @code
    <?php
        $form = new Depage\HtmlForm\HtmlForm('myform');

        // add HTML
        $form->addHtml('<div id="myimage"></div>');

        // process form
        $form->process();

        // Display the form.
        echo ($form);
 * @endcode
 */
class Html
{
    // {{{ variables
    /**
     * @brief HTML code to be printed
     **/
    private $htmlString;
    /**
     * @brief Whether to sanitize the HTML or pass it through raw
     **/
    private $sanitize = true;
    /**
     * @brief Allowed tags for sanitization
     **/
    private $allowedTags = null;
    // }}}

    // {{{ __construct()
    /**
     * @brief html class constructor
     *
     * @param string $htmlString HTML to be printed
     * @param array $parameters Optional parameters for sanitization
     **/
    public function __construct(string $htmlString, $parameters = [])
    {
        $this->htmlString = $htmlString;

        if (isset($parameters['sanitize']) && is_bool($parameters['sanitize'])) {
            $this->sanitize = $parameters['sanitize'];
        }
        if (isset($parameters['allowedTags']) && is_array($parameters['allowedTags'])) {
            $this->allowedTags = $parameters['allowedTags'];
        }
    }
    // }}}

    // {{{ __toString()
    /**
     * @brief   Renders element to sanitized HTML.
     *
     * @return string $this->htmlString Sanitized HTML-rendered element
     **/
    public function __toString(): string
    {
        if ($this->sanitize && class_exists(HtmlSanitizer::class)) {
            $sanitizer = new HtmlSanitizer();
            $sanitizer->loadHTML($this->htmlString);
            $sanitizer->cleanHTML($this->allowedTags, null, false);
            return (string) mb_trim($sanitizer);
        }

        return (string) $this->htmlString;
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
