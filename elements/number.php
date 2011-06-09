<?php
/**
 * @file    elements/number.php
 * @brief   number input element
 **/

namespace depage\htmlform\elements;

/**
 * @brief HTML number input type.
 *
 * @todo find a workaround for localization of decimal separator
 **/
class number extends text {
    // {{{ variables
    /**
     * @brief Minimum range HTML attribute.
     **/
    protected $min;
    /**
     * @brief Maximum range HTML attribute.
     **/
    protected $max;
    /**
     * @brief Step HTML attribute.
     **/
    protected $step;
    // }}}

    // {{{ setDefaults()
    /**
     * @brief   collects initial values across subclasses.
     *
     * @return  void
     **/
    protected function setDefaults() {
        parent::setDefaults();

        $this->defaults['defaultValue'] = 0;
        $this->defaults['min']          = null;
        $this->defaults['max']          = null;
        $this->defaults['step']         = null;
        $this->defaults['errorMessage'] = 'Please enter a valid number!';
    }
    // }}}

    // {{{ __toString()
    /**
     * @brief   Renders element to HTML.
     *
     * @return  (string) HTML rendered element
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
    // }}}

    // {{{ htmlMin()
    /**
     * @brief   Renders HTML min attribute.
     *
     * @return  (string) HTML min attribute
     **/
    protected function htmlMin() {
        return ($this->min === null) ? "" : " min=\"" . $this->htmlEscape($this->min) . "\"";
    }
    // }}}

    // {{{ htmlMax()
    /**
     * @brief   Renders HTML max attribute.
     *
     * @return  (string) HTML max attribute
     **/
    protected function htmlMax() {
        return ($this->max === null) ? "" : " max=\"" . $this->htmlEscape($this->max) . "\"";
    }
    // }}}

    // {{{ htmlStep()
    /**
     * @brief   Renders HTML step attribute.
     *
     * @return  (string) HTML step attribute
     **/
    protected function htmlStep() {
        return ($this->step === null) ? "" : " step=\"" . $this->htmlEscape($this->step) . "\"";
    }
    // }}}

    // {{{ validatorCall()
    /**
     * @brief   custom validator call
     *
     * Number specific validator call (includes min and max values)
     *
     * @return  (bool) validaton result
     **/
    protected function validatorCall() {
        $parameters = array(
            'min' => $this->min,
            'max' => $this->max,
        );
        return $this->validator->validate($this->value, $parameters);
    }
    // }}}

    // {{{ typeCastValue()
    /**
     * @brief   Converts value to element specific type.
     *
     * @return  void
     **/
    protected function typeCastValue() {
        $this->value = (float) $this->value;
    }
    // }}}
}
