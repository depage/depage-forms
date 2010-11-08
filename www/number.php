<?php

require_once('textClass.php');

/**
 * HTML number input type.
 **/
class number extends textClass {
    protected $min;
    protected $max;
    protected $step;

    /**
     * Renders element to HTML.
     *
     * @return string of HTML rendered element
     **/
    public function __toString() {
        $classes = $this->getClasses();
        $requiredChar = $this->getRequiredChar();
        $min = ($this->min !== null) ? " min=\"$this->min\"" : "";
        $max = ($this->max !== null) ? " max=\"$this->max\"" : "";
        $step = ($this->step !== null) ? " step=\"$this->step\"" : "";

        return "<p id=\"$this->formName-$this->name\" class=\"$classes\"><label><span class=\"label\">$this->label$requiredChar</span><input name=\"$this->name\" type=\"$this->type\"$max$min$step value=\"$this->value\"></label></p>";
    }

    /**
     * Overrides parent::setDefaults()
     *
     * return void
     **/
    protected function setDefaults() {
        parent::setDefaults();
        $this->defaults['min'] = null;
        $this->defaults['max'] = null;
        $this->defaults['step'] = null;
    }
}
