<?php

namespace depage\htmlform\elements;

/**
 * HTML datetimelocal input type.
 **/
class datetimelocal extends text {
    /**
     * Renders element to HTML. datetime-local needs its own rendering method 
     * because of the minus sign.
     *
     * @return string of HTML rendered element
     **/
    public function render($value, $attributes, $requiredChar, $class) {
        return "<p id=\"{$this->formName}-{$this->name}\" class=\"$class\">" .
            "<label>" .
                "<span class=\"label\">{$this->label}$requiredChar</span>" .
                "<input name=\"$this->name\" type=\"datetime-local\"$attributes value=\"$value\">" .
            "</label>" .
        "</p>\n";
    }
}
