<?php 
/**
 * The abstract class textClass is parent to text based input element classes.
 * It contains basic HTML rendering functionality.
 **/

namespace depage\htmlform\abstracts;

abstract class textClass extends inputClass {
    /**
     * @param $name input elements' name
     * @param $parameters array of input element parameters, HTML attributes, validator specs etc.
     * @param $formName name of the parent HTML form. Used to identify the element once it's rendered.
     **/
    public function __construct($name, $parameters, $formName) {
        parent::__construct($name, $parameters, $formName);
        
        // textClass elements have values of type string
        $this->defaultValue = (isset($parameters['defaultValue'])) ? $parameters['defaultValue'] : '';
    }

    /**
     * Renders element to HTML.
     *
     * @return string of HTML rendered element
     **/
    public function render($value, $attributes, $requiredChar, $class) {
        return "<p id=\"{$this->formName}-{$this->name}\" class=\"$class\">" .
            "<label>" .
                "<span class=\"label\">{$this->label}$requiredChar</span>" .
                "<input name=\"$this->name\" type=\"$this->type\"$attributes value=\"$value\">" .
            "</label>" .
        "</p>\n";
    }

    /**
     * Converts value to element specific type.
     *
     * @param $value value to be converted
     * @return string converted value
     **/
    protected function typeCastValue($value) {
        return (string) $value;
    }
}
