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
 * By default the HTML is printed as-is, so you should only add HTML
 * that you control yourself. If you want to add HTML from an untrusted
 * source, set the sanitize-parameter to true (or use
 * Depage::HtmlForm::Abstracts::Container::addSanitizedHtml()). All tags
 * and attributes that are not allowed are then removed before rendering.
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
    private $sanitize = false;
    /**
     * @brief Allowed tags for sanitization
     **/
    private $allowedTags = null;
    // }}}

    // {{{ __construct()
    /**
     * @brief html class constructor
     *
     * By default the HTML is printed as-is, so you should only add HTML
     * that you control yourself. Set the sanitize-parameter to true to
     * remove all tags and attributes that are not allowed before rendering.
     *
     * @param string $htmlString HTML to be printed
     * @param array $parameters Optional parameters for sanitization:
     *                          - sanitize (bool): whether to sanitize the HTML (default: false)
     *                          - allowedTags (array): tags that are allowed inside of the HTML;
     *                            defaults to HtmlDom::DEFAULT_ALLOWED_TAGS when sanitizing
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
     * @brief   Renders element to HTML.
     *
     * Renders the element to HTML. The HTML is sanitized first when the
     * sanitize-parameter was set to true, otherwise it is printed as-is.
     *
     * @return string $this->htmlString HTML-rendered element
     **/
    public function __toString(): string
    {
        if ($this->sanitize && class_exists(HtmlSanitizer::class)) {
            $sanitizer = new HtmlSanitizer();
            $sanitizer->loadHTML($this->htmlString);
            $sanitizer->cleanHTML($this->allowedTags, null, false);

            if (function_exists('mb_trim')) {
                return (string) mb_trim($sanitizer);
            }

            return (string) trim($sanitizer);
        }

        return (string) $this->htmlString;
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
