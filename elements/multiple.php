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
     * @param $form parent form object.
     **/
    public function __construct($name, $parameters, $form) {
        parent::__construct($name, $parameters, $form);

        $this->list = (isset($parameters['list']) && is_array($parameters['list'])) ? $parameters['list'] : array();
    }

    /**
     * collects initial values across subclasses.
     **/
    protected function setDefaults() {
        parent::setDefaults();

        // multiple-choice-elements have values of type array
        $this->defaults['defaultValue'] = array();
        $this->defaults['skin']         = 'checkbox';
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

        $options    = $this->htmlEscape($options);
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

            foreach($options as $index => $option) {
                $selected = (is_array($value) && (in_array($index, $value))) ? " checked=\"yes\"" : '';

                $list .= "<span>" .
                    "<label>" .
                        "<input type=\"checkbox\" name=\"{$this->name}[]\"{$inputAttributes} value=\"{$index}\"{$selected}>" .
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
        $marker             = $this->htmlMarker();
        $label              = $this->htmlLabel();
        $list               = $this->htmlList();
        $wrapperAttributes  = $this->htmlWrapperAttributes();
        $errorMessage       = $this->htmlErrorMessage();

        if ($this->skin === 'select') {
            // render HTML select

            $inputAttributes = $this->htmlInputAttributes();

            return "<p {$wrapperAttributes}>" .
                "<label>" .
                    "<span class=\"label\">{$label}{$marker}</span>" .
                    "<select multiple name=\"{$this->name}[]\"{$inputAttributes}>{$list}</select>" .
                "</label>" .
                $errorMessage .
            "</p>\n";
        } else {
            // render HTML checkbox

            return "<p {$wrapperAttributes}>" .
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
        $attributes = '';

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
