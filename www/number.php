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
    public function render($value, $requiredAttribute, $requiredChar, $class) {
        if ($this->min !== null) {
            $min = " min=\"$this->min\"";
            if ($value < $this->min) {
                $value = $this->min;
            }
        } else {
            $min = "";
        }
        if ($this->max !== null) {
            $max = " max=\"$this->max\"";
            if ($value > $this->max) {
                $value = $this->max;
            }
        } else {
            $max = "";
        }
        $step = ($this->step !== null) ? " step=\"$this->step\"" : "";

        return "<p id=\"$this->formName-$this->name\" class=\"$class\">" .
            "<label>" . 
                "<span class=\"label\">$this->label$requiredChar</span>" . 
                "<input name=\"$this->name\" type=\"$this->type\"$max$min$step$requiredAttribute value=\"$value\">" .
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
        $this->defaults['defaultValue'] = 0;
        $this->defaults['min'] = null;
        $this->defaults['max'] = null;
        $this->defaults['step'] = null;
    }
}
