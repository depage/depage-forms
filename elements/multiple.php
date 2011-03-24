<?php 

namespace depage\htmlform\elements;

use depage\htmlform\abstracts;

/**
 * HTML-multiple-choice input type i.e. checkbox and select.
 **/
class multiple extends abstracts\input {
    /** 
     * Contains list of selectable options.
     **/
    protected $list = array();

    /**
     * @param $name input elements' name
     * @param $parameters array of input element parameters, HTML attributes, validator specs etc.
     * @param $formName name of the parent HTML form. Used to identify the element once it's rendered.
     **/
    public function __construct($name, $parameters, $formName) {
        parent::__construct($name, $parameters, $formName);
        
        // multiple-choice-elements have values of type array
        $this->defaultValue = (isset($parameters['defaultvalue']))  ? $parameters['defaultvalue']   : array();
        $this->list         = (isset($parameters['list']))          ? $parameters['list']           : array();
        $this->skin         = (isset($parameters['skin']))          ? $parameters['skin']           : 'checkbox';
    }

    /**
     * Renders HTML - option list part of select element. Works recursively in
     * case of optgroups. If no parameters are parsed, it uses the list
     * attribute of this element.
     *
     * @param $options array of list elements and subgroups
     * @param $value value to be marked as selected
     * @return (string) options-part of the HTML-select-element
     **/
    protected function htmlList($options = null, $value = null) {
        if ($value == null)     $value      = $this->htmlValue();
        if ($options == null)   $options    = $this->list;

        $options    = parent::htmlList($options);
        $list       = '';

        if ($this->skin === "select") {
            foreach($options as $index => $option) {
                if (is_array($option)) {
                    $list       .= "<optgroup label=\"{$index}\">" . $this->htmlList($option, $value) . "</optgroup>";
                } else {
                    $selected   = (in_array($index, $value)) ? ' selected' : '';
                    $list       .= "<option value=\"{$index}\"{$selected}>{$option}</option>";
                }
            }
        } else {
            $inputAttributes = $this->htmlInputAttributes();
            $labelAttributes = $this->htmlLabelAttributes();

            foreach($options as $index => $option) {
                $selected = (is_array($value) && (in_array($index, $value))) ? " checked=\"yes\"" : '';

                $list .= "<span>" .
                "<label{$labelAttributes}>" .
                        "<input type=\"checkbox\" name=\"{$this->name}[]\" {$inputAttributes} value=\"{$index}\"{$selected}>" .
                        "<span>{$option}</span>" .
                    "</label>" .
                "</span>";
            }
        }
        return $list;
    }

    /**
     * Renders element to HTML.
     *
     * @return string of HTML rendered element
     **/
    public function __toString() {
        $classes            = $this->htmlClasses();
        $marker             = $this->htmlMarker();
        $errorMessage       = $this->htmlErrorMessage();
        $label              = $this->htmlLabel();
        $formName           = $this->htmlFormName();
        $list               = $this->htmlList();

        if ($this->skin === 'select') {
            // render HTML select
            $inputAttributes = $this->htmlInputAttributes();
            $labelAttributes = $this->htmlLabelAttributes();

            return "<p id=\"{$formName}-{$this->name}\" class=\"{$classes}\">" .
                "<label{$labelAttributes}>" .
                    "<span class=\"label\">{$label}{$marker}</span>" .
                    "<select multiple name=\"{$this->name}[]\" {$inputAttributes}>{$list}</select>" .
                "</label>" .
                $errorMessage .
            "</p>\n";

        } else {
            // render HTML checkbox

            return "<p id=\"{$formName}-{$this->name}\" class=\"{$classes}\">" .
                "<span class=\"label\">{$label}{$marker}</span>" .
                "<span>{$list}</span>" .
                $errorMessage .
            "</p>\n";
        }
    }

    /**
     * Returns string of HTML attributes for input element. (overrides
     * input->htmlInputAttributes to avoid awkward HTML5 checkbox validation (all
     * boxes need to be checked if required))
     *
     * @return string HTML attribute
     **/
    protected function htmlInputAttributes() {
        $attributes = "data-errorMessage=\"" . htmlentities($this->errorMessage, ENT_QUOTES) . "\"";

        // HTML5 validator hack
        if ($this->required && $this->skin != 'select') $attributes .= " required";
        if ($this->autofocus)                           $attributes .= " autofocus";

        return $attributes;
    }

    /**
     * Converts value to element specific type.
     **/
    protected function typeCastValue() {
        if ($this->value == "") {
            $this->value = array();
        } else {
            $this->value = (array) $this->value;
        }
    }
}
