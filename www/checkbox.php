<?php 

require_once ('inputClass.php');

class checkbox extends checkboxClass {
    private $optionList = array();
    
    public function __construct($name, $parameters, $formName) {
        parent::__construct($name, $parameters, $formName);
        $this->optionList = (isset($parameters['optionList'])) ? $parameters['optionList'] : '';
    }

    public function __toString() {
        $options = '';
                foreach($this->optionList as $index => $option) {
                    $selected = (in_array($index, $this->value)) ? ' checked=\"yes\"' : '';
                    $options .= "<div><label><input type=\"checkbox\" name=\"$this->name[]\" value=\"$index\"$selected><span>$option</span></label></div>";
                }

        $classes = $this->getClasses();
        return "<p id=\"$this->formName-$this->name\" class=\"$classes\"><span class=\"label\">$this->label</span><div>$options</div></p>";
    }
}
