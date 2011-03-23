<?php
namespace depage\htmlform\elements;

/**
 * HTML hidden input type.
 **/
class hidden extends text {
    public function __toString() {
        $formName   = $this->htmlFormName();
        $classes    = $this->htmlClasses();
        $value      = $this->htmlValue();

        return "<input name=\"{$this->name}\" id=\"{$formName}-{$this->name}\" type=\"{$this->type}\" class=\"{$classes}\" value=\"{$value}\">\n";
    }
}
