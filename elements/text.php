<?php

namespace depage\htmlform\elements;

use depage\htmlform\abstracts;

/**
 * HTML text input type.
 **/
class text extends abstracts\input {
     /**
     * @param $name input elements' name
     * @param $parameters array of input element parameters, HTML attributes, validator specs etc.
     * @param $formName name of the parent HTML form. Used to identify the element once it's rendered.
     **/
    public function __construct($name, $parameters, $formName) {
        parent::__construct($name, $parameters, $formName);

        // textClass elements have values of type string
        $this->defaultValue = (isset($parameters['defaultValue'])) ? $parameters['defaultValue'] : '';
    }

    /**
     * Renders element to HTML.
     *
     * @return string of HTML rendered element
     **/
    public function __toString() {
        return "<p id=\"{$this->formName}-{$this->name}\" class=\"" . $this->getRenderedClasses() . "\">" .
            "<label>" .
                "<span class=\"label\">" . $this->label . $this->getRenderedRequiredChar() . "</span>" .
                "<input name=\"$this->name\" type=\"$this->type\"" . $this->getRenderedAttributes() . " value=\"" . $this->getRenderedValue() . "\">" .
            "</label>" .
            $this->getRenderedErrorMessage() .
        "</p>\n";
    }

    /**
     * Converts value to element specific type.
     **/
    protected function typeCastValue() {
        $this->value = (string) $this->value;
    }
}
