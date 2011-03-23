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
     * Renders HTML - option list part of select element.
     * Works recursively in case of optgroups.
     *
     * @param $optionsArray array of list elements and subgroups
     * @param $value value that is to be marked as selecteѕ
     * @return (string) options-part of the HTML-select-element
     **/
    private function htmlList($optionsArray, $value) {
        $options = '';
        foreach($optionsArray as $index => $option) {
            if (is_array($option)) {
                $options .= "<optgroup label=\"{$index}\">" . $this->htmlList($option, $value) . "</optgroup>";
            } else {
                $selected = (in_array($index, $value)) ? ' selected' : '';
                $options .= "<option value=\"{$index}\"{$selected}>{$option}</option>";
            }
        }
        return $options;
    }

    /**
     * Renders element to HTML.
     *
     * @return string of HTML rendered element
     **/
    public function __toString() {
        $list               = '';
        $value              = $this->htmlValue();
        $classes            = $this->htmlClasses();
        $marker             = $this->htmlMarker();
        $inputAttributes    = $this->htmlInputAttributes();
        $errorMessage       = $this->htmlErrorMessage();
        $labelAttributes    = $this->htmlLabelAttributes();
        $label              = $this->htmlLabel();
        $formName           = $this->htmlFormName();

        if ($this->skin === 'select') {
            // render HTML select

            $list = $this->htmlList($this->list, $value);

            return "<p id=\"{$formName}-{$this->name}\" class=\"{$classes}\">" .
                "<label{$labelAttributes}>" .
                    "<span class=\"label\">{$label}{$marker}</span>" .
                    "<select multiple name=\"{$this->name}[]\"{$inputAttributes}>{$list}</select>" .
                "</label>" .
                $errorMessage .
            "</p>\n";

        } else {
            // render HTML checkbox

            foreach($this->list as $index => $option) {
                $selected = (is_array($value) && (in_array($index, $value))) ? " checked=\"yes\"" : '';

                $list .= "<span>" .
                "<label{$labelAttributes}>" .
                        "<input type=\"checkbox\" name=\"{$this->name}[]\"{$inputAttributes} value=\"{$index}\"{$selected}>" .
                        "<span>{$option}</span>" .
                    "</label>" .
                "</span>";
            }

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
        $attributes = "data-errorMessage=\"$this->errorMessage\"";

        // HTML5 validator hack
        if ($this->required && $this->skin != 'select') $attributes .= " required";
        if ($this->autofocus)                           $attributes .= " autofocus";

        $attributes = htmlentities($attributes, ENT_QUOTES);
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
