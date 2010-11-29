<?php 

require_once('text.php');
require_once('email.php');
require_once('hidden.php');
require_once('url.php');
require_once('tel.php');
require_once('inputClass.php');
require_once('textarea.php');
require_once('password.php');
require_once('range.php');
require_once('number.php');

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
