<?php

/**
 * @file    elements/number.php
 * @brief   number input element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace Depage\HtmlForm\Elements;

/**
 * @brief HTML number input type.
 *
 * Class for HTML5 input type "number".
 *
 * Usage
 * -----
 *
 * @code
    <?php
        $form = new Depage\HtmlForm\HtmlForm('myform');

        // add a number field
        $form->addNumber('any', array(
            'label' => 'Any number',
        ));

        // add a number field with restrictions
        $form->addNumber('even', array(
            'label' => 'Even number between 0 and 10, inclusive',
            'min'   => 0,
            'max'   => 10,
            'step'  => 2,
        ));

        // process form
        $form->process();

        // Display the form.
        echo ($form);
    @endcode
 **/
class Number extends Text
{
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
     * @return void
     **/
    protected function setDefaults(): void
    {
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
     * @return string HTML rendered element
     **/
    public function __toString(): string
    {
        $value              = $this->htmlValue();
        $type               = strtolower($this->type);
        $inputAttributes    = $this->htmlInputAttributes();
        $marker             = $this->htmlMarker();
        $label              = $this->htmlLabel();
        $min                = $this->htmlMin();
        $max                = $this->htmlMax();
        $step               = $this->htmlStep();
        $wrapperAttributes  = $this->htmlWrapperAttributes();
        $errorMessage       = $this->htmlErrorMessage();
        $list               = $this->htmlList();
        $helpMessage        = $this->htmlHelpMessage();

        return "<p {$wrapperAttributes}>" .
            "<label>" .
                "<span class=\"depage-label\">{$label}{$marker}</span>" .
                "<input name=\"{$this->name}\" type=\"{$type}\"{$max}{$min}{$step}{$inputAttributes} value=\"{$value}\">" .
                $list .
            "</label>" .
            $errorMessage .
            $helpMessage .
        "</p>\n";
    }
    // }}}

    // {{{ htmlMin()
    /**
     * @brief   Renders HTML min attribute.
     *
     * @return string HTML min attribute
     **/
    protected function htmlMin(): string
    {
        return ($this->min === null) ? "" : " min=\"" . $this->htmlEscape($this->min) . "\"";
    }
    // }}}

    // {{{ htmlMax()
    /**
     * @brief   Renders HTML max attribute.
     *
     * @return string HTML max attribute
     **/
    protected function htmlMax(): string
    {
        return ($this->max === null) ? "" : " max=\"" . $this->htmlEscape($this->max) . "\"";
    }
    // }}}

    // {{{ htmlStep()
    /**
     * @brief   Renders HTML step attribute.
     *
     * @return string HTML step attribute
     **/
    protected function htmlStep(): string
    {
        return ($this->step === null) ? "" : " step=\"" . $this->htmlEscape($this->step) . "\"";
    }
    // }}}

    // {{{ validatorCall()
    /**
     * @brief   custom validator call
     *
     * Number specific validator call (includes min and max values)
     *
     * @return bool validaton result
     **/
    protected function validatorCall(): bool
    {
        $parameters = [
            'min' => $this->min,
            'max' => $this->max,
        ];

        return $this->validator->validate($this->value, $parameters);
    }
    // }}}

    // {{{ typeCastValue()
    /**
     * @brief   Converts value to element specific type.
     *
     * Based on (parseFloat) http://www.php.net/manual/en/function.floatval.php#84793
     *
     * @return void
     **/
    protected function typeCastValue(): void
    {
        $ptString = $this->value;

        $pString = str_replace(" ", "", $ptString);

        // assume if we have more than one mark it's a thousand mark -> remove them
        if (substr_count($pString, ",") > 1) {
            $pString = str_replace(",", "", $pString);
        }
        if (substr_count($pString, ".") > 1) {
            $pString = str_replace(".", "", $pString);
        }

        // assume last entry is decimal mark
        $commaset = strrpos($pString, ',');
        $pointset = strrpos($pString, '.');

        // remove all remaining marks but the decimal mark
        if ($commaset > $pointset) {
            $pString = str_replace(".", "", $pString);
        } elseif ($commaset < $pointset) {
            $pString = str_replace(",", "", $pString);
        }

        // normalize to dot
        $pString = str_replace(",", ".", $pString);

        if ($pString !== "" && preg_match("/^[-+]?\d*\.?\d*$/", $pString)) {
            $this->value = (float) $pString;
        } else {
            $this->value = null;
        }
    }
    // }}}

    // {{{ getStringValue()
    /**
     * @brief getStringValue
     *
     * converts number value to normalized string to avoid localization issues
     * when converting to string
     *
     * @return string
     **/
    public function getStringValue(): string
    {
        $decimals = 0;

        if ($this->step !== null && ($pos = strpos($this->step, ".")) !== false) {
            $decimals = strlen(substr($this->step, $pos + 1));
        }

        return number_format($this->getValue(), $decimals, '.', '');
    }
    // }}}

    // {{{ isEmpty()
    /**
     * @brief  custom empty check
     *
     * Number specific empty check (allows for zero int/float values)
     *
     * @return bool empty-check result
     **/
    public function isEmpty(): bool
    {
        return (
            empty($this->value)
            && $this->value !== 0
            && $this->value !== .0
        );
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
