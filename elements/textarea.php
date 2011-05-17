<?php
/**
 * @file    textarea.php
 * @brief   textarea input element
 **/

namespace depage\htmlform\elements;

/**
 * @brief HTML textarea element.
 **/
class textarea extends text {
    // {{{ setDefaults()
    /**
     * @brief   collects initial values across subclasses
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
