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
    public function render($value, $requiredAttribute, $requiredChar, $class) {
        $rows = ($this->rows !== null) ? " rows=\"$this->rows\"" : "";
        $cols = ($this->cols !== null) ? " cols=\"$this->cols\"" : "";

        return "<p id=\"$this->formName-$this->name\" class=\"$class\">" .
            "<label>" .
                "<span class=\"label\">$this->label$requiredChar</span>" .
                "<textarea name=\"$this->name\"$requiredAttribute $rows$cols>$this->value</textarea>" .
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
