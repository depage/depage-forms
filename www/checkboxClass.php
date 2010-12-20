<?php 

require_once ('inputClass.php');
require_once ('select.php');
require_once ('checkbox.php');
require_once ('radio.php');

/**
 * The abstract class checkboxClass is parent to choice based input element
 * classes. It contains basic HTML rendering functionality.
 **/
abstract class checkboxClass extends inputClass {
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
        
        // checkboxClass elements have values of type array
        $this->defaultValue = (isset($parameters['defaultValue']))  ? $parameters['defaultValue']   : array();
        $this->optionList   = (isset($parameters['optionList']))    ? $parameters['optionList']     : array();
    }

    /**
     * Renders element to HTML.
     *
     * @return string of HTML rendered element
     **/
    public function render($value, $requiredAttribute, $requiredChar, $class) {
        $options = '';

        foreach($this->optionList as $index => $option) {
            $selected = (is_array($value) && (in_array($index, $value))) ? " checked=\"yes\"" : '';
            $options .= "<span>" .
                "<label>" .
                    "<input type=\"$this->type\" name=\"$this->name[]\"$requiredAttribute value=\"$index\"$selected>" .
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
