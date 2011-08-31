<?php
/**
 * @file    textarea.php
 * @brief   textarea input element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace depage\htmlform\elements;

/**
 * @brief HTML textarea element.
 *
 * @section usage
 *
 * @code
 * <?php
 *     $form = new depage\htmlform\htmlform('myform');
 *
 *     // add a textarea field
 *     $form->addTextarea('comment', array(
 *         'label' => 'Comment box',
 *     ));
 *
 *     // process form
 *     $form->process();
 *
 *     // Display the form.
 *     echo ($form);
 * ?>
 * @endcode
 **/
class textarea extends text {
    // {{{ setDefaults()
    /**
     * @brief   collects initial values across subclasses
     *
     * The constructor loops through these and creates settable class
     * attributes at runtime. It's a compact mechanism for initialising
     * a lot of variables.
     *
     * @return  void
     **/
    protected function setDefaults() {
        parent::setDefaults();

        $this->defaults['rows'] = null;
        $this->defaults['cols'] = null;
    }
    // }}}

    // {{{ __toString()
    /**
     * @brief   Renders element to HTML.
     *
     * @return  (string) HTML rendered element
     **/
    public function __toString() {
        $label              = $this->htmlLabel();
        $marker             = $this->htmlMarker();
        $inputAttributes    = $this->htmlInputAttributes();
        $value              = $this->htmlValue();
        $rows               = $this->htmlRows();
        $cols               = $this->htmlCols();
        $wrapperAttributes  = $this->htmlWrapperAttributes();
        $errorMessage       = $this->htmlErrorMessage();

        return "<p {$wrapperAttributes}>" .
            "<label>" .
                "<span class=\"label\">{$label}{$marker}</span>" .
                "<textarea name=\"{$this->name}\"{$inputAttributes}{$rows}{$cols}>{$value}</textarea>" .
            "</label>" .
            $errorMessage .
        "</p>\n";
    }
    // }}}

    // {{{ htmlRows()
    /**
     * @brief   Renders HTML rows attribute
     *
     * @return  (string) HTML rows attribute
     **/
    protected function htmlRows() {
        return ($this->rows === null) ? "" : " rows=\"" . $this->htmlEscape($this->rows) . "\"";
    }
    // }}}

    // {{{ htmlCols()
    /**
     * @brief   Renders HTML cols attribute
     *
     * @return  (string) HTML cols attribute
     **/
    protected function htmlCols() {
        return ($this->cols === null) ? "" : " cols=\"" . $this->htmlEscape($this->cols) . "\"";
    }
    // }}}
}

/* vim:set ft=php fenc=UTF-8 sw=4 sts=4 fdm=marker et : */
