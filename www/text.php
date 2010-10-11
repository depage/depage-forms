<?php

class text extends textClass {
    public function __toString() {
        $classes = $this->getClasses();
        return "<p id=\"$this->formName-$this->name\" class=\"$classes\"><label><span>$this->label</span><input name=\"$this->name\" type=\"$this->type\" value=\"$this->value\"></label></p>";
    }
}
