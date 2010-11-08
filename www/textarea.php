<?php

require_once('textClass.php');

/**
 * HTML textarea element.
 **/
class textarea extends textClass {
    /**
     * Renders element to HTML.
     *
     * @return string of HTML rendered element
     **/
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

    /**
     * Overrides parent::setDefaults()
     *
     * return void
     **/
    public function setDefaults() {
        parent::setDefaults();
        $this->defaults['rows'] = null;
        $this->defaults['cols'] = null;
    }
}
