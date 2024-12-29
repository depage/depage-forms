<?php

/**
 * @file    hidden.php
 * @brief   hidden input element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace Depage\HtmlForm\Elements;

/**
 * @brief HTML hidden input type.
 *
 * Class for the HTML input-type "hidden".
 *
 * Usage
 * -----
 *
 * @code
    <?php
        $form = new Depage\HtmlForm\HtmlForm('myform');

        // add a hidden field
        $form->addHidden('nonce', array(
            'defaultValue' => 'XD5fkk',
        ));

        // process form
        $form->process();

        // Display the form.
        echo ($form);
    @endcode
 **/
class Hidden extends Text
{
    // {{{ __toString()
    /**
     * @brief   Renders element to HTML.
     *
     * @return string HTML-rendered element
     **/
    public function __toString(): string
    {
        $formName   = $this->htmlFormName();
        $classes    = $this->htmlClasses();
        $value      = $this->htmlValue();
        $type       = strtolower($this->type);

        return "<input name=\"{$this->name}\" id=\"{$formName}-{$this->name}\" type=\"{$type}\" class=\"{$classes}\" value=\"{$value}\">\n";
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
