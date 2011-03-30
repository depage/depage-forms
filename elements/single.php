<?php 

namespace depage\htmlform\elements;

use depage\htmlform\abstracts;

/** 
 * HTML-single-choice input type i.e. radio and select.
 **/
class single extends abstracts\input {
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

        // single-choice-elements have values of type string
        $this->defaultValue = (isset($parameters['defaultvalue']))  ? $parameters['defaultvalue']   : "";
        $this->list         = (isset($parameters['list']))          ? $parameters['list']           : array();
        $this->skin         = (isset($parameters['skin']))          ? $parameters['skin']           : "radio";
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
                    $selected   = ((string) $index === $value) ? ' selected' : '';
                    $list       .= "<option value=\"{$index}\"{$selected}>{$option}</option>";
                }
            }
        } else {
            $inputAttributes = $this->htmlInputAttributes();

            foreach($options as $index => $option) {
                // typecasted for non-associative arrays
                $selected = ((string) $index === $value) ? " checked=\"yes\"" : '';

                $list .= "<span>" .
                    "<label>" .
                        "<input type=\"radio\" name=\"{$this->name}\"{$inputAttributes} value=\"{$index}\"{$selected}>" .
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

        if ($this->skin === "select") {
            // render HTML select
            $inputAttributes = $this->htmlInputAttributes();

            return "<p {$wrapperAttributes}>" .
                "<label>" .
                    "<span class=\"label\">{$label}{$marker}</span>" .
                    "<select name=\"{$this->name}\"{$inputAttributes}>{$list}</select>" .
                "</label>" .
                $errorMessage .
            "</p>\n";
        } else {
            // render HTML radio button list

            return "<p {$wrapperAttributes}>" .
                "<label>" .
                    "<span class=\"label\">{$label}{$marker}</span>" .
                    "<span>{$list}</span>" .
                "</label>" .
                $errorMessage .
            "</p>\n";
        }
    }

    /**
     * Converts value to element specific type.
     **/
    protected function typeCastValue() {
        $this->value = (string) $this->value;
    }
}
