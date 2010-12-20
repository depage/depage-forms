<?php 

require_once('elementText.php');
require_once('elementEmail.php');
require_once('elementHidden.php');
require_once('elementUrl.php');
require_once('elementTel.php');
require_once('inputClass.php');
require_once('elementTextarea.php');
require_once('elementPassword.php');
require_once('elementRange.php');
require_once('elementNumber.php');

/**
 * The abstract class textClass is parent to text based input element classes.
 * It contains basic HTML rendering functionality.
 **/
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
    public function render($value, $requiredAttribute, $requiredChar, $class) {
        return "<p id=\"$this->formName-$this->name\" class=\"$class\">" .
            "<label>" .
                "<span class=\"label\">$this->label$requiredChar</span>" .
                "<input name=\"$this->name\" type=\"$this->type\"$requiredAttribute value=\"$value\">" .
            "</label>" .
        "</p>\n";
    }
}
