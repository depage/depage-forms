<?php 

require_once ('inputClass.php');

/** 
 * HTML select element.
 **/
class select extends checkboxClass {
    /**
     * @param $name select - elements' name
     * @param $parameters array of input element parameters, HTML attributes, validator specs etc.
     * @param $formName name of the parent HTML form. Used to identify the element once it's rendered.
     **/
    public function __construct($name, $parameters, $formName) {
        parent::__construct($name, $parameters, $formName);
        $this->multiple = ((isset($parameters['multiple'])) && ($parameters['multiple'] === true)) ? true : false;
    }
    
    /**
     * Renders element to HTML.
     *
     * @return string of HTML rendered element
     **/
    public function render($value, $requiredAttribute, $requiredChar, $class) {
        $options = '';
        foreach($this->optionList as $index => $option) {
            $selected = (in_array($index, $value)) ? ' selected' : '';
            $options .= "<option value=\"$index\"$selected>$option</option>";
        }

        $multiple = ($this->multiple) ? ' multiple' : '';
        return "<p id=\"$this->formName-$this->name\" class=\"$class\">" .
            "<label>" .
                "<span class=\"label\">$this->label$requiredChar</span>" .
                "<select$multiple name=\"$this->name[]\"$requiredAttribute>$options</select>" .
            "</label>" .
        "</p>\n";
    }
}
