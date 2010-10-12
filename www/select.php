<?php 

require_once ('inputClass.php');

class select extends checkboxClass {
    private $optionList = array();
    
    public function __construct($name, $parameters, $formName) {
        parent::__construct($name, $parameters, $formName);
        $this->optionList = (isset($parameters['optionList'])) ? $parameters['optionList'] : '';
    }

    public function __toString() {
        $options = '';
                foreach($this->optionList as $index => $option) {
                    $selected = ($index == $this->value) ? ' selected' : '';
                    $options .= "<option value=\"$index\"$selected>$option</option>";
                }

        $classes = $this->getClasses();
        return "<p id=\"$this->formName-$this->name\" class=\"$classes\"><label><span>$this->label</span><select name=\"$this->name\">$options</select></label></p>";
    }
}
