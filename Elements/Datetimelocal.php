<?php

/**
 * @file    datetimelocal.php
 * @brief   datetime-local input element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace Depage\HtmlForm\Elements;

/**
 * @brief   HTML datetime-local input type.
 *
 * @todo    dummy - no validator implemented yet
 **/
class Datetimelocal extends Text
{
    /**
     * @brief   Renders element to HTML.
     *
     * datetime-local needs its own rendering method because of the minus sign.
     *
     * @return string HTML-rendered element
     **/
    public function __toString(): string
    {
        $wrapperAttributes  = $this->htmlWrapperAttributes();
        $label              = $this->htmlLabel();
        $marker             = $this->htmlMarker();
        $value              = $this->htmlValue();
        $inputAttributes    = $this->htmlInputAttributes();
        $errorMessage       = $this->htmlErrorMessage();
        $helpMessage        = $this->htmlHelpMessage();

        return "<p {$wrapperAttributes}>" .
            "<label>" .
                "<span class=\"depage-label\">{$label}{$marker}</span>" .
                "<input name=\"{$this->name}\" type=\"datetime-local\"{$inputAttributes} value=\"{$value}\">" .
            "</label>" .
            $errorMessage .
            $helpMessage .
        "</p>\n";
    }
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
