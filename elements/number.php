<?php

namespace depage\htmlform\elements;

/**
 * HTML number input type.
 **/
class number extends text {
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
    public function __construct($name, &$parameters, $formName) {
        parent::__construct($name, $parameters, $formName);

        $this->defaultValue = (isset($parameters['defaultvalue']))  ? $parameters['defaultvalue']   : 0;
        $this->min          = (isset($parameters['min']))           ? $parameters['min']            : null;
        $this->max          = (isset($parameters['max']))           ? $parameters['max']            : null;
        $this->step         = (isset($parameters['step']))          ? $parameters['step']           : null;
        $this->errorMessage = (isset($parameters['errormessage']))  ? $parameters['errormessage']   : 'Please enter a valid number!';
    }

    /**
     * Renders element to HTML.
     *
     * @return string of HTML rendered element
     **/
    public function __toString() {
        $value      = $this->htmlValue();
        $classes    = $this->htmlClasses();
        $attributes = $this->htmlInputAttributes();
        $marker     = $this->htmlMarker();
        $min        = " min=\"$this->min\"";
        $max        = " max=\"$this->max\"";
        $step       = ($this->step !== null) ? " step=\"$this->step\"" : "";

        return "<p id=\"{$this->formName}-{$this->name}\" class=\"{$classes}\">" .
            "<label" . $this->htmlLabelAttributes() . ">" .
                "<span class=\"label\">{$this->label}{$marker}</span>" .
                "<input name=\"{$this->name}\" type=\"{$this->type}\"{$max}{$min}{$step}{$attributes} value=\"{$value}\">" .
            "</label>" .
            $this->htmlErrorMessage() .
        "</p>\n";
    }

    /**
     * Overrides parent method to add min and max values
     **/
    protected function validatorCall() {
        return $this->validator->validate($this->value, $this->min, $this->max);
    }

    /**
     * Converts value to element specific type.
     **/
    protected function typeCastValue() {
        $this->value = (float) $this->value;
    }
}
