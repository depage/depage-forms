<?php
namespace depage\htmlform\elements;

use depage\htmlform\abstracts;

/**
 * HTML hidden input type.
 **/
class hidden extends abstracts\textClass {
    public function render($value, $attributes, $requiredChar, $class) {
        return "<input name=\"$this->name\" id=\"$this->formName-$this->name\" type=\"$this->type\" class=\"$class\" value=\"$value\">\n";
    }
}
