<?php

/**
 * @file    textarea.php
 * @brief   textarea input element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace Depage\HtmlForm\Elements;

/**
 * @brief HTML textarea element.
 *
 * Usage
 * -----
 *
 * @code
    <?php
        $form = new Depage\HtmlForm\HtmlForm('myform');

        // add a textarea field
        $form->addTextarea('comment', array(
            'label' => 'Comment box',
        ));

        // process form
        $form->process();

        // Display the form.
        echo ($form);
    @endcode
 **/
class Textarea extends Text
{
    // {{{ variables
    /**
     * @brief HTML rows attribute
     **/
    protected $rows;

    /**
     * @brief HTML cols attribute
     **/
    protected $cols;

    /**
     * @brief wether to autogrow textarea or not
     **/
    protected $autogrow = false;
    // }}}
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

        $this->defaults['rows'] = null;
        $this->defaults['cols'] = null;
        $this->defaults['autogrow'] = false;
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
        $label              = $this->htmlLabel();
        $marker             = $this->htmlMarker();
        $inputAttributes    = $this->htmlInputAttributes();
        $value              = $this->htmlValue();
        $rows               = $this->htmlRows();
        $cols               = $this->htmlCols();
        $wrapperAttributes  = $this->htmlWrapperAttributes();
        $errorMessage       = $this->htmlErrorMessage();
        $helpMessage        = $this->htmlHelpMessage();

        return "<p {$wrapperAttributes}>" .
            "<label>" .
                "<span class=\"depage-label\">{$label}{$marker}</span>" .
                "<textarea name=\"{$this->name}\"{$inputAttributes}{$rows}{$cols}>{$value}</textarea>" .
            "</label>" .
            $errorMessage .
            $helpMessage .
        "</p>\n";
    }
    // }}}

    // {{{ htmlDataAttributes()
    /**
     * @brief   Returns dataAttr escaped as attribute string
     **/
    protected function htmlDataAttributes(): string
    {
        $options = [];
        $options['autogrow'] = $this->autogrow;

        $this->dataAttr['textarea-options'] = json_encode($options);

        return parent::htmlDataAttributes();
    }
    // }}}

    // {{{ htmlRows()
    /**
     * @brief   Renders HTML rows attribute
     *
     * @return string HTML rows attribute
     **/
    protected function htmlRows(): string
    {
        return ($this->rows === null) ? "" : " rows=\"" . $this->htmlEscape($this->rows) . "\"";
    }
    // }}}
    // {{{ htmlCols()
    /**
     * @brief   Renders HTML cols attribute
     *
     * @return string HTML cols attribute
     **/
    protected function htmlCols(): string
    {
        return ($this->cols === null) ? "" : " cols=\"" . $this->htmlEscape($this->cols) . "\"";
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
