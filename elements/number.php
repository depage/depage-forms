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
    public function __construct($name, &$parameters, $form) {
        parent::__construct($name, $parameters, $form);

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
        $value              = $this->htmlValue();
        $inputAttributes    = $this->htmlInputAttributes();
        $marker             = $this->htmlMarker();
        $label              = $this->htmlLabel();
        $min                = $this->htmlMin();
        $max                = $this->htmlMax();
        $step               = $this->htmlStep();
        $wrapperAttributes  = $this->htmlWrapperAttributes();
        $errorMessage       = $this->htmlErrorMessage();
        $list               = $this->htmlList();

        return "<p {$wrapperAttributes}>" .
            "<label>" .
                "<span class=\"label\">{$label}{$marker}</span>" .
                "<input name=\"{$this->name}\" type=\"{$this->type}\"{$max}{$min}{$step}{$inputAttributes} value=\"{$value}\">" .
                $list .
            "</label>" .
            $errorMessage .
        "</p>\n";
    }

    /**
     * Returns string of HTML min attribute.
     **/
    protected function htmlMin() {
        return ($this->min === null) ? "" : " min=\"" . htmlentities($this->min, ENT_QUOTES) . "\"";
    }

    /**
     * Returns string of HTML max attribute.
     **/
    protected function htmlMax() {
        return ($this->max === null) ? "" : " max=\"" . htmlentities($this->max, ENT_QUOTES) . "\"";
    }

    /**
     * Returns string of HTML step attribute.
     **/
    protected function htmlStep() {
        return ($this->step === null) ? "" : " step=\"" . htmlentities($this->step, ENT_QUOTES) . "\"";
    }

    /**
     * Overrides parent method to add min and max values
     **/
    protected function validatorCall() {
        $parameters = array(
            'min' => $this->min,
            'max' => $this->max,
        );
        return $this->validator->validate($this->value, $parameters);
    }

    /**
     * Converts value to element specific type.
     **/
    protected function typeCastValue() {
        $this->value = (float) $this->value;
    }
}
