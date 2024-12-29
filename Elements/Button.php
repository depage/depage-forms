<?php

/**
 * @file    text.php
 * @brief   text input element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace Depage\HtmlForm\Elements;

use Depage\HtmlForm\Abstracts;

/**
 * @brief HTML text input type.
 *
 * Class for the HTML input-type "text".
 *
 * Usage
 * -----
 *
 * @code
    <?php
        $form = new Depage\HtmlForm\HtmlForm('myform');

        // add a text field
        $form->addText('food', array(
            'label' => 'Favourite food',
        ));

        // process form
        $form->process();

        // Display the form.
        echo ($form);
    @endcode
 **/
class Button extends \Depage\HtmlForm\Abstracts\Input
{
    protected $defaultValue = "";

    // {{{ setDefaults()
    /**
     * @brief   collects initial values across subclasses
     *
     * The constructor loops through these and creates settable class
     * attributes at runtime. It's a compact mechanism for initialising
     * a lot of variables.
     *
     * @return void
     **/
    protected function setDefaults(): void
    {
        parent::setDefaults();

        $this->defaults['defaultValue'] = false;
    }
    // }}}

    // {{{ setValue()
    /**
     * @brief   set the boolean element value
     *
     * Sets the current input elements' value. Converts it to boolean if
     * necessary.
     *
     * @param  mixed $newValue new element value
     * @return bool  $this->value    converted value
     **/
    public function setValue(mixed $newValue): bool
    {
        if (is_bool($newValue)) {
            $this->value = $newValue;
        } elseif ($newValue === "true") {
            $this->value = true;
        } else {
            $this->value = false;
        }

        return $this->value;
    }
    // }}}

    // {{{ __toString()
    /**
     * @brief   Renders element to HTML.
     *
     * @return string HTML rendered element
     **/
    public function __toString(): string
    {
        $value              = "true";
        $type               = "submit";
        $inputAttributes    = $this->htmlInputAttributes();
        $marker             = $this->htmlMarker();
        $label              = $this->htmlLabel();
        $wrapperAttributes  = $this->htmlWrapperAttributes();
        $errorMessage       = $this->htmlErrorMessage();
        $helpMessage        = $this->htmlHelpMessage();

        return "<p {$wrapperAttributes}>" .
            "<button name=\"{$this->name}\" type=\"submit\"{$inputAttributes} value=\"{$value}\">{$label}</button>" .
            $errorMessage .
            $helpMessage .
        "</p>\n";
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
