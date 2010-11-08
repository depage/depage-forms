<?php

require_once('textClass.php');

/**
 * HTML hidden input type.
 **/
class hidden extends textClass {
    public function __toString() {
        $classes = $this->getClasses();
        return "<input name=\"$this->name\" id=\"$this->formName-$this->name\" type=\"$this->type\" class=\"$classes\" value=\"$this->value\">\n";
    }
}
