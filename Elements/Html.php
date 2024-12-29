<?php

/**
 * @file    html.php
 * @brief   html element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace Depage\HtmlForm\Elements;

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
    @endcode
 **/
class Html
{
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
     * @param string $htmlString HTML to be printed
     **/
    public function __construct(string $htmlString)
    {
        $this->htmlString = $htmlString;
    }
    // }}}

    // {{{ __toString()
    /**
     * @brief   Renders element to HTML.
     *
     * @return string $this->htmlString HTML-rendered element
     **/
    public function __toString(): string
    {
        return (string) $this->htmlString;
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
