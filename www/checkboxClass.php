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
     * Overrides parent::setDefaults()
     *
     * return void
     **/
    protected function setDefaults() {
        parent::setDefaults();

        // checkboxClass elements have values of type array
        $this->defaults['value'] = array();
        $this->defaults['optionList'] = array();
    }

    /**
     * Renders element to HTML.
     *
     * @return string of HTML rendered element
     **/
    public function __toString() {
        $options = '';

        foreach($this->optionList as $index => $option) {
            $selected = (in_array($index, $this->value)) ? " checked=\"yes\"" : '';
            $options .= "<span>" .
                "<label>" .
                    "<input type=\"$this->type\" name=\"$this->name[]\"" . $this->getRequiredAttribute() . " value=\"$index\"$selected>" .
                    "<span>$option</span>" .
                "</label>" .
            "</span>";
        }

        return "<p id=\"$this->formName-$this->name\" class=\"" . $this->getClasses() . "\">" .
            "<span class=\"label\">$this->label" . $this->getRequiredChar() . "</span>" .
            "<span>$options</span>" .
        "</p>\n";
    }

    /** 
     * Returns true if $option is selected.
     *
     * @param $option option list key
     * @return boolean - true if selected - false if not
     **/
    public function isChecked($options) {
        return (in_array($options, $this->value));
    }
}
