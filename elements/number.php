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
     * collects initial values across subclasses.
     **/
    protected function setDefaults() {
        parent::setDefaults();

        $this->defaults['defaultValue'] = 0;
        $this->defaults['min']          = null;
        $this->defaults['max']          = null;
        $this->defaults['step']         = null;
        $this->defaults['errorMessage'] = 'Please enter a valid number!';
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
        return ($this->min === null) ? "" : " min=\"" . $this->htmlEscape($this->min) . "\"";
    }

    /**
     * Returns string of HTML max attribute.
     **/
    protected function htmlMax() {
        return ($this->max === null) ? "" : " max=\"" . $this->htmlEscape($this->max) . "\"";
    }

    /**
     * Returns string of HTML step attribute.
     **/
    protected function htmlStep() {
        return ($this->step === null) ? "" : " step=\"" . $this->htmlEscape($this->step) . "\"";
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
