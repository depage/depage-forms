<?php
namespace depage\htmlform\elements;

/**
 * HTML hidden input type.
 **/
class hidden extends text {
    public function __toString() {
        return "<input name=\"$this->name\" id=\"$this->formName-$this->name\" type=\"$this->type\" class=\"" . $this->htmlClasses() . "\" value=\"" . $this->htmlValue() . "\">\n";
    }
}
