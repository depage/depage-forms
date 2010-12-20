<?php

require_once('textClass.php');

/**
 * HTML hidden input type.
 **/
class inputHidden extends textClass {
    public function render($value, $requiredAttribute, $requiredChar, $class) {
        return "<input name=\"$this->name\" id=\"$this->formName-$this->name\" type=\"$this->type\" class=\"$class\" value=\"$value\">\n";
    }
}
