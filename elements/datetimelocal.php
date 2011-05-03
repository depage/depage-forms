<?php
/**
 * @file    datetimelocal.php
 * @brief   datetime-local input element
 **/

namespace depage\htmlform\elements;

/**
 * @brief   HTML datetime-local input type.
 *
 * @todo    dummy - no validator implemented yet
 **/
class datetimelocal extends text {
    /**
     * @brief   Renders element to HTML.
     *
     * datetime-local needs its own rendering method because of the minus sign.
     *
     * @return  (string) HTML-rendered element
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
