<?php

namespace depage\htmlform\elements;

use depage\htmlform\abstracts;

/**
 * HTML text input type.
 **/
class text extends abstracts\input {
    /**
     * HTML placeholder attribute
     **/
     protected $placeholder;

     /**
     * @param $name input elements' name
     * @param $parameters array of input element parameters, HTML attributes, validator specs etc.
     * @param $formName name of the parent HTML form. Used to identify the element once it's rendered.
     **/
    public function __construct($name, $parameters, $formName) {
        parent::__construct($name, $parameters, $formName);

        // textClass elements have values of type string
        $this->defaultValue = (isset($parameters['defaultValue']))  ? $parameters['defaultValue']   : '';
        $this->placeholder  = (isset($parameters['placeholder']))   ? $parameters['placeholder']    : false;
        $this->list         = (isset($parameters['list']))          ? $parameters['list']           : false;
    }

    /**
     * Renders element to HTML.
     *
     * @return string of HTML rendered element
     **/
    public function __toString() {
        return "<p id=\"{$this->formName}-{$this->name}\" class=\"" . $this->htmlClasses() . "\">" .
            "<label>" .
                "<span class=\"label\">" . $this->label . $this->htmlRequiredChar() . "</span>" .
                "<input name=\"$this->name\" type=\"$this->type\"" . $this->htmlAttributes() . " value=\"" . $this->htmlValue() . "\">" .
                $this->htmlList();
            "</label>" .
            $this->htmlErrorMessage() .
        "</p>\n";
    }

    /**
     * Renders HTML datalist.
     **/
    protected function htmlList() {
        if ($this->list && is_array($this->list)) {
            $htmlList = "<datalist id=\"{$this->name}-list\">";

            foreach ($this->list as $index => $option) {
                // if the array is associative
                if (0 !== count(array_diff_key($this->list, array_keys(array_keys($this->list))))) {
                    $htmlList .= "<option value=\"$index\" label=\"$option\">";
                } else {
                    $htmlList .= "<option value=\"$option\">";
                }
            }

            $htmlList .= "</datalist>";
        } else {
            $htmlList = "";
        }

        return $htmlList;
    }

    /**
     * Adds placeholder to the attribute list if it's set.
     **/
    protected function htmlAttributes() {
        $attributes = parent::htmlAttributes();

        if ($this->placeholder) $attributes .= " placeholder=\"$this->placeholder\"";
        if ($this->list)        $attributes .= " list=\"{$this->name}-list\"";

        $attributes .= $this->validator->getPatternAttribute();

        return $attributes;
    }

    /**
     * Converts value to element specific type.
     **/
    protected function typeCastValue() {
        $this->value = (string) $this->value;
    }
}
