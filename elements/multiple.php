<?php 

namespace depage\htmlform\elements;

use depage\htmlform\abstracts;

/**
 * HTML-multiple-choice input type i.e. checkbox and select.
 **/
class multiple extends abstracts\inputClass {
    /** 
     * Contains list of selectable options.
     **/
    protected $optionList = array();

    /**
     * @param $name input elements' name
     * @param $parameters array of input element parameters, HTML attributes, validator specs etc.
     * @param $formName name of the parent HTML form. Used to identify the element once it's rendered.
     **/
    public function __construct($name, $parameters, $formName) {
        parent::__construct($name, $parameters, $formName);
        
        // multiple-choice-elements have values of type array
        $this->defaultValue = (isset($parameters['defaultValue']))  ? $parameters['defaultValue']   : array();
        $this->optionList   = (isset($parameters['optionList']))    ? $parameters['optionList']     : array();
        $this->skin         = (isset($parameters['skin']))          ? $parameters['skin']           : 'checkbox';
    }

    /**
     * Renders element to HTML.
     *
     * @return string of HTML rendered element
     **/
    public function render($value, $attributes, $requiredChar, $class) {
        $options = '';

        if ($this->skin === 'select') {
            foreach($this->optionList as $index => $option) {
                $selected = (in_array($index, $value)) ? ' selected' : '';
                $options .= "<option value=\"$index\"$selected>$option</option>";
            }

            return "<p id=\"$this->formName-$this->name\" class=\"$class\">" .
                "<label>" .
                    "<span class=\"label\">$this->label$requiredChar</span>" .
                    "<select multiple name=\"$this->name[]\"$attributes>$options</select>" .
                "</label>" .
            "</p>\n";
        } else {
            foreach($this->optionList as $index => $option) {
                $selected = (is_array($value) && (in_array($index, $value))) ? " checked=\"yes\"" : '';
                $options .= "<span>" .
                    "<label>" .
                        "<input type=\"checkbox\" name=\"$this->name[]\"$attributes value=\"$index\"$selected>" .
                        "<span>$option</span>" .
                    "</label>" .
                "</span>";
            }

            return "<p id=\"$this->formName-$this->name\" class=\"$class\">" .
                "<span class=\"label\">$this->label$requiredChar</span>" .
                "<span>$options</span>" .
            "</p>\n";
        }
    }

    /**
     * Converts value to element specific type.
     *
     * @param $value value to be converted
     * @return array converted value
     **/
    protected function typeCastValue($value) {
        if ($value == "") {
            return array();
        } else {
            return (array) $value;
        }
    }
}
