<?php 

namespace depage\htmlform\elements;

use depage\htmlform\abstracts;

/**
 * HTML single checkbox input type.
 **/
class boolean extends abstracts\inputClass {
    /**
     * @param $name input elements' name
     * @param $parameters array of input element parameters, HTML attributes, validator specs etc.
     * @param $formName name of the parent HTML form. Used to identify the element once it's rendered.
     **/
    public function __construct($name, $parameters, $formName) {
        parent::__construct($name, $parameters, $formName);
        
        // boolean-elements have values of type boolean
        $this->defaultValue = (isset($parameters['defaultValue'])) ? $parameters['defaultValue'] : (boolean) false;
    }

    /**
     * Renders element to HTML.
     *
     * @return string of HTML rendered element
     **/
    public function render($value, $attributes, $requiredChar, $class) {
        $selected = ($value === true) ? " checked=\"yes\"" : '';

        return "<p id=\"$this->formName-$this->name\" class=\"$class\">" .
            "<span>" .
                "<label>" .
                    "<input type=\"checkbox\" name=\"$this->name\"$attributes value=\"true\"$selected>" .
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
