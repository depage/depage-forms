<?php

require_once('textClass.php');

/**
 * HTML hidden input type.
 **/
class hidden extends textClass {
    public function __toString() {
        return "<input name=\"$this->name\" id=\"$this->formName-$this->name\" type=\"$this->type\" class=\"" . $this->getClasses() . "\" value=\"$this->value\">\n";
    }
}
