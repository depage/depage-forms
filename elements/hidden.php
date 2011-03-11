<?php
namespace depage\htmlform\elements;

/**
 * HTML hidden input type.
 **/
class hidden extends text {
    public function render($value, $attributes, $requiredChar, $class) {
        return "<input name=\"$this->name\" id=\"$this->formName-$this->name\" type=\"$this->type\" class=\"$class\" value=\"$value\">\n";
    }
}
