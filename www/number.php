<?php

require_once('textClass.php');

class number extends range {
    public function __toString() {
        $classes = $this->getClasses();
        $requiredChar = $this->getRequiredChar();
        return "<p id=\"$this->formName-$this->name\" class=\"$classes\"><label><span class=\"label\">$this->label$requiredChar</span><input name=\"$this->name\" type=\"$this->type\" min=\"$this->min\" max=\"$this->max\" step=\"$this->step\" value=\"$this->value\"></label></p>";
    }

    protected function setDefaults() {
        parent::setDefaults();
        $this->defaults['min'] = 0;
        $this->defaults['max'] = 100;
        $this->defaults['step'] = 1;
    }
}
