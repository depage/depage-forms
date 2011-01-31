<?php
/**
 * HTML hidden input type.
 **/

namespace depage\htmlform\elements;

use depage\htmlform\abstracts;

class hidden extends abstracts\textClass {
    public function render($value, $requiredAttribute, $requiredChar, $class) {
        return "<input name=\"$this->name\" id=\"$this->formName-$this->name\" type=\"$this->type\" class=\"$class\" value=\"$value\">\n";
    }
}
