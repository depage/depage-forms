<?php

require_once('number.php');

class range extends number {

    public function __toString() {
        $classes = $this->getClasses();
        $requiredChar = $this->getRequiredChar();
        return "<p id=\"$this->formName-$this->name\" class=\"$classes\"><label><span class=\"label\">$this->label$requiredChar</span><input name=\"$this->name\" type=\"$this->type\" min=\"$this->min\" max=\"$this->max\" step=\"$this->step\" value=\"$this->value\"></label></p>\n";
    }
}
