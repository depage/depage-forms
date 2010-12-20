<?php 

require_once ('inputClass.php');

/**
 * HTML single checkbox input type.
 **/
class inputBoolean extends inputClass {
    /**
     * @param $name input elements' name
     * @param $parameters array of input element parameters, HTML attributes, validator specs etc.
     * @param $formName name of the parent HTML form. Used to identify the element once it's rendered.
     **/
    public function __construct($name, $parameters, $formName) {
        parent::__construct($name, $parameters, $formName);
        
        $this->defaultValue = (isset($parameters['defaultValue'])) ? $parameters['defaultValue'] : false;
    }

    /**
     * Renders element to HTML.
     *
     * @return string of HTML rendered element
     **/
    public function render($value, $requiredAttribute, $requiredChar, $class) {
        $selected = ($value === true) ? " checked=\"yes\"" : '';

        return "<p id=\"$this->formName-$this->name\" class=\"$class\">" .
            "<span>" .
                "<label>" .
                    "<input type=\"checkbox\" name=\"$this->name\"$requiredAttribute value=\"true\"$selected>" .
                    "<span class=\"label\">$this->label$requiredChar</span>" .
                "</label>" .
            "</span>" .
        "</p>\n";
    }

    /**
     * Sets the current input elements value. Converts it to boolean if
     * necessary.
     *
     * @param $newValue contains the new value
     * @return $this->value converted value
     **/
    public function setValue($newValue) {
        if (is_bool($newValue)) {
            $this->value = $newValue;
        } else if ($newValue === "true") {
            $this->value = true;
        } else {
            $this->value = false;
        }

        return $this->value;
    }
}
