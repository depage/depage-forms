<?php
/**
 * @file    elements/number.php
 * @brief   number input element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace depage\htmlform\elements;

/**
 * @brief HTML number input type.
 *
 * Class for HTML5 input type "number".
 *
 * @section usage
 *
 * @code
 * <?php
 *     $form = new depage\htmlform\htmlform('myform');
 *
 *     // add a number field
 *     $form->addNumber('any', array(
 *         'label' => 'Any number',
 *     ));
 *
 *     // add a number field with restrictions
 *     $form->addNumber('even', array(
 *         'label' => 'Even number between 0 and 10, inclusive',
 *         'min'   => 0,
 *         'max'   => 10,
 *         'step'  => 2,
 *     ));
 *
 *     // process form
 *     $form->process();
 *
 *     // Display the form.
 *     echo ($form);
 * ?>
 * @endcode
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
     * The constructor loops through these and creates settable class
     * attributes at runtime. It's a compact mechanism for initialising
     * a lot of variables.
     *
     * @return  void
     **/
    protected function setDefaults() {
        parent::setDefaults();

        $this->defaults['defaultValue'] = 0;
        $this->defaults['min']          = null;
        $this->defaults['max']          = null;
        $this->defaults['step']         = null;
        $this->defaults['errorMessage'] = _('Please enter a valid number');
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
     * Based on (parseFloat) http://www.php.net/manual/en/function.floatval.php#84793
     *
     * @return  void
     **/
    protected function typeCastValue() {
        $ptString = $this->value;

        $pString = str_replace(" ", "", $ptString);

        if (substr_count($pString, ",") > 1) {$pString = str_replace(",", "", $pString);}
        if (substr_count($pString, ".") > 1) {$pString = str_replace(".", "", $pString);}

        $pregResult = array();

        $commaset = strpos($pString,',');
        if ($commaset === false) {$commaset = -1;}

        $pointset = strpos($pString,'.');
        if ($pointset === false) {$pointset = -1;}

        $pregResultA = array();
        $pregResultB = array();

        if ($pointset < $commaset) {
            preg_match('#(([-]?[0-9]+(\.[0-9])?)+(,[0-9]+)?)#', $pString, $pregResultA);
        }
        preg_match('#(([-]?[0-9]+(,[0-9])?)+(\.[0-9]+)?)#', $pString, $pregResultB);
        if ((isset($pregResultA[0]) && (!isset($pregResultB[0])
                        || strstr($preResultA[0],$pregResultB[0]) == 0
                        || !$pointset))) {
            $numberString = $pregResultA[0];
            $numberString = str_replace('.','',$numberString);
            $numberString = str_replace(',','.',$numberString);
        }
        elseif (isset($pregResultB[0]) && (!isset($pregResultA[0])
                    || strstr($pregResultB[0],$preResultA[0]) == 0
                    || !$commaset)) {
            $numberString = $pregResultB[0];
            $numberString = str_replace(',','',$numberString);
        }
        $this->value = (float) $numberString;
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
