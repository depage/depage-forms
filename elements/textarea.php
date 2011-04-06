<?php

namespace depage\htmlform\elements;

/**
 * HTML textarea element.
 **/
class textarea extends text {
    /**
     * collects initial values across subclasses.
     **/
    protected function setDefaults() {
        parent::setDefaults();

        $this->defaults['rows'] = null;
        $this->defaults['cols'] = null;
    }

    /**
     * Renders element to HTML.
     *
     * @return string of HTML rendered element
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

    /**
     * Returns string of HTML rows attribute.
     **/
    protected function htmlRows() {
        return ($this->rows === null) ? "" : " rows=\"" . htmlentities($this->rows, ENT_QUOTES) . "\"";
    }

    /**
     * Returns string of HTML cols attribute.
     **/
    protected function htmlCols() {
        return ($this->cols === null) ? "" : " cols=\"" . htmlentities($this->cols, ENT_QUOTES) . "\"";
    }
}
