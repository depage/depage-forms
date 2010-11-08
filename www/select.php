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
    public function __toString() {
        $options = '';
        foreach($this->optionList as $index => $option) {
            $selected = (is_array($this->value) && in_array($index, $this->value)) ? ' selected' : '';
            $options .= "<option value=\"$index\"$selected>$option</option>";
        }

        $multiple = ($this->multiple) ? ' multiple' : '';
        return "<p id=\"$this->formName-$this->name\" class=\"" . $this->getClasses() . "\">" .
            "<label>" .
                "<span class=\"label\">$this->label" . $this->getRequiredChar() . "</span>" .
                "<select$multiple name=\"$this->name[]\"" . $this->getRequiredAttribute() . ">$options</select>" .
            "</label>" .
        "</p>\n";
    }
}
