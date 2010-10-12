<?php 

require_once ('inputClass.php');

class checkbox extends checkboxClass {
    public function __toString() {
        $options = '';
        foreach($this->optionList as $index => $option) {
            $selected = (in_array($index, $this->value)) ? ' checked=\"yes\"' : '';
            $options .= "<span><label><input type=\"$this->type\" name=\"$this->name[]\" value=\"$index\"$selected><span>$option</span></label></span>";
        }

        $classes = $this->getClasses();
        return "<p id=\"$this->formName-$this->name\" class=\"$classes\"><span class=\"label\">$this->label</span><span>$options</span></p>";
    }
}
