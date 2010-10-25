<?php 

require_once ('inputClass.php');
require_once ('select.php');
require_once ('checkbox.php');
require_once ('radio.php');

abstract class checkboxClass extends inputClass {
    protected $optionList = array();

    protected function setDefaults() {
        parent::setDefaults();
        $this->defaults['value'] = array();
        $this->defaults['optionList'] = array();
    }
    
    public function __toString() {
        $options = '';
        foreach($this->optionList as $index => $option) {
            $selected = (in_array($index, $this->value)) ? ' checked=\"yes\"' : '';
            $options .= "<span><label><input type=\"$this->type\" name=\"$this->name[]\" value=\"$index\"$selected><span>$option</span></label></span>";
        }

        $classes = $this->getClasses();
        $requiredChar = $this->getRequiredChar();
        return "<p id=\"$this->formName-$this->name\" class=\"$classes\"><span class=\"label\">$this->label<em>$requiredChar</em></span><span>$options</span></p>\n";
    }

    public function isChecked($option) {
        return (in_array($option, $this->value));
    }
}
