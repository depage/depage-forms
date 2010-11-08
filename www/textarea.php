<?php

require_once('textClass.php');

class textarea extends textClass {
   public function __toString() {
        $rows = ($this->rows !== null) ? " rows=\"$this->rows\"" : "";
        $cols = ($this->cols !== null) ? " cols=\"$this->cols\"" : "";

        return "<p id=\"$this->formName-$this->name\" class=\"" . $this->getClasses() . "\">" .
            "<label>" .
                "<span class=\"label\">$this->label" . $this->getRequiredChar() . "</span>" .
                "<textarea name=\"$this->name\"" . $this->getRequiredAttribute() . " $rows$cols>$this->value</textarea>" .
            "</label>" .
        "</p>\n";
    }

    public function setDefaults() {
        parent::setDefaults();
        $this->defaults['rows'] = null;
        $this->defaults['cols'] = null;
    }
}
