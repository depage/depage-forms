<?php

require_once('textClass.php');

/**
 * HTML number input type.
 **/
class number extends textClass {
    /**
     * Minimum range HTML attribute.
     **/
    protected $min;
    /**
     * Maximum range HTML attribute.
     **/
    protected $max;
    /**
     * Step HTML attribute.
     **/
    protected $step;

    /**
     * Renders element to HTML.
     *
     * @return string of HTML rendered element
     **/
    public function __toString() {
        $min = ($this->min !== null) ? " min=\"$this->min\"" : "";
        $max = ($this->max !== null) ? " max=\"$this->max\"" : "";
        $step = ($this->step !== null) ? " step=\"$this->step\"" : "";

        return "<p id=\"$this->formName-$this->name\" class=\"" . $this->getClasses() . "\">" .
            "<label>" . 
                "<span class=\"label\">$this->label" . $this->getRequiredChar() . "</span>" . 
                "<input name=\"$this->name\" type=\"$this->type\"$max$min$step" . $this->getRequiredAttribute() . " value=\"$this->value\">" .
            "</label>" .
        "</p>";
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
