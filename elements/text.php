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
    public function __construct($name, &$parameters, $form) {
        parent::__construct($name, $parameters, $form);

        // textClass elements have values of type string
        $this->defaultValue = (isset($parameters['defaultvalue']))                          ? $parameters['defaultvalue']   : '';
        $this->placeholder  = (isset($parameters['placeholder']))                           ? $parameters['placeholder']    : false;
        $this->list         = (isset($parameters['list']) && is_array($parameters['list'])) ? $parameters['list']           : false;
    }

    /**
     * Renders element to HTML.
     *
     * @return string of HTML rendered element
     **/
    public function __toString() {
        $value              = $this->htmlValue();
        $inputAttributes    = $this->htmlInputAttributes();
        $marker             = $this->htmlMarker();
        $label              = $this->htmlLabel();
        $list               = $this->htmlList();
        $wrapperAttributes  = $this->htmlWrapperAttributes();
        $errorMessage       = $this->htmlErrorMessage();

        return "<p {$wrapperAttributes}>" .
            "<label>" .
                "<span class=\"label\">{$label}{$marker}</span>" .
                "<input name=\"{$this->name}\" type=\"{$this->type}\"{$inputAttributes} value=\"{$value}\">" .
                $list .
            "</label>" .
            $errorMessage .
        "</p>\n";
    }

    /**
     * Renders HTML datalist.
     **/
    protected function htmlList($options = null) {
        if ($this->list && is_array($this->list)) {
            $formName   = $this->htmlFormName();
            $options    = parent::htmlList($this->list);

            $htmlList = "<datalist id=\"{$formName}-{$this->name}-list\">";

            foreach ($options as $index => $option) {
                // associative arrays have index as value
                if (is_int($index)) {
                    $htmlList .= "<option value=\"{$option}\">";
                } else {
                    $htmlList .= "<option value=\"{$index}\" label=\"{$option}\">";
                }
            }

            $htmlList .= "</datalist>";
        } else {
            $htmlList = "";
        }
        return $htmlList;
    }

    /**
     * Renders text element specific attributes.
     **/
    protected function htmlInputAttributes() {
        $attributes = parent::htmlInputAttributes();

        if ($this->placeholder) $attributes .= " placeholder=\"{$this->placeholder}\"";
        if ($this->list)        $attributes .= " list=\"{$this->formName}-{$this->name}-list\"";

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
