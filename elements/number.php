<?php

namespace depage\htmlform\elements;

use depage\htmlform\abstracts;

/**
 * HTML number input type.
 **/
class number extends abstracts\textClass {
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
     * @param $name input elements' name
     * @param $parameters array of input element parameters, HTML attributes, validator specs etc.
     * @param $formName name of the parent HTML form. Used to identify the element once it's rendered.
     **/
    public function __construct($name, $parameters, $formName) {
        parent::__construct($name, $parameters, $formName);
        
        $this->defaultValue = (isset($parameters['defaultValue'])) ? $parameters['defaultValue'] : 0;
        $this->min = (isset($parameters['min'])) ? $parameters['min'] : null;
        $this->max = (isset($parameters['max'])) ? $parameters['max'] : null;
        $this->step = (isset($parameters['step'])) ? $parameters['step'] : null;
    }

    /**
     * Renders element to HTML.
     *
     * @return string of HTML rendered element
     **/
    public function render($value, $attributes, $requiredChar, $class) {
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
                "<input name=\"$this->name\" type=\"$this->type\"$max$min$step$attributes value=\"$value\">" .
            "</label>" .
        "</p>";
    }
}
