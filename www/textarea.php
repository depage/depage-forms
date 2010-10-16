<?php

require_once('textClass.php');

class textarea extends textClass {
   public function __toString() {
        $classes = $this->getClasses();
        $requiredChar = $this->getRequiredChar();
        return "<p id=\"$this->formName-$this->name\" class=\"$classes\"><label><span class=\"label\">$this->label</span><em>$requiredChar</em><textarea name=\"$this->name\" rows=\"$this->rows\" cols=\"$this->cols\">$this->value</textarea></label></p>";
    }

    public function setDefaults() {
        parent::setDefaults();
        $this->defaults['rows'] = 3;
        $this->defaults['cols'] = 20;
    }
}
