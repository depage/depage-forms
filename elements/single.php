<?php 

namespace depage\htmlform\elements;

use depage\htmlform\abstracts;

/** 
 * HTML-single-choice input type i.e. radio and select.
 **/
class single extends abstracts\inputClass {
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
        
        // single-choice-elements have values of type string
        $this->defaultValue = (isset($parameters['defaultValue']))  ? $parameters['defaultValue']   : "";
        $this->optionList   = (isset($parameters['optionList']))    ? $parameters['optionList']     : array();
        $this->skin         = (isset($parameters['skin']))          ? $parameters['skin']           : "radio";
    }
    
    /**
     * Renders element to HTML.
     *
     * @return string of HTML rendered element
     **/
    public function render($value, $attributes, $requiredChar, $class) {
        $options = '';

        if ($this->skin === "select") {
            foreach($this->optionList as $index => $option) {
                $selected = ($index === $value) ? ' selected' : '';
                $options .= "<option value=\"$index\"$selected>$option</option>";
            }

            return "<p id=\"$this->formName-$this->name\" class=\"$class\">" .
                "<label>" .
                    "<span class=\"label\">$this->label$requiredChar</span>" .
                    "<select name=\"$this->name\"$attributes>$options</select>" .
                "</label>" .
            "</p>\n";
        } else {
            foreach($this->optionList as $index => $option) {
                $selected = ($index === $value) ? " checked=\"yes\"" : '';
                $options .= "<span>" .
                    "<label>" .
                        "<input type=\"radio\" name=\"$this->name\"$attributes value=\"$index\"$selected>" .
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
}
