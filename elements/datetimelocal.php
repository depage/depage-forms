<?php

namespace depage\htmlform\elements;

/**
 * HTML datetimelocal input type.
 *
 * @todo dummy - no validator implemented yet
 **/
class datetimelocal extends text {
    /**
     * Renders element to HTML. datetime-local needs its own rendering method 
     * because of the minus sign.
     *
     * @return string of HTML rendered element
     **/
    public function __toString() {
        $wrapperAttributes  = $this->htmlWrapperAttributes();
        $label              = $this->htmlLabel();
        $marker             = $this->htmlMarker();
        $value              = $this->htmlValue();
        $inputAttributes    = $this->htmlInputAttributes();
        $errorMessage       = $this->htmlErrorMessage();

        return "<p {$wrapperAttributes}>" .
            "<label>" .
                "<span class=\"label\">{$label}{$marker}</span>" .
                "<input name=\"{$this->name}\" type=\"datetime-local\"{$inputAttributes} value=\"{$value}\">" .
            "</label>" .
            $errorMessage .
        "</p>\n";
    }
}
