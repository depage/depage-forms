<?php
/**
 * @file    boolean.php
 * @brief   boolean input element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace depage\htmlform\elements;

use depage\htmlform\abstracts;

/**
 * @brief HTML single checkbox input type.
 *
 * Class for a single "checkbox". If the boolean input is required it has to
 * be clicked to sumbit the form succesfully. It may e.g. be used for specific
 * terms a user has to accept to register.
 * 
 * @section usage
 *
 * @code
 * <?php
 *     $form = new depage\htmlform\htmlform('myform');
 *
 *     // add a boolean field
 *     $form->addBoolean('newsletter', array(
 *         'label' => 'I want to receive news by mail',
 *     ));
 *
 *     // add a required boolean field
 *     $form->addBoolean('acceptTerms', array(
 *         'label' => 'I accept the following terms',
 *         'required' => true,
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
class boolean extends abstracts\input {
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
        $this->defaults['defaultValue'] = false;
        $this->defaults['errorMessage'] = _('Please check this box if you want to proceed');
    }
    // }}}

    // {{{ __toString()
    /**
     * @brief   Renders element to HTML.
     *
     * @return  (string) HTML-rendered element
     **/
    public function __toString() {
        $inputAttributes    = $this->htmlInputAttributes();
        $label              = $this->htmlLabel();
        $marker             = $this->htmlMarker();
        $wrapperAttributes  = $this->htmlWrapperAttributes();
        $errorMessage       = $this->htmlErrorMessage();

        $selected = ($this->htmlValue() === true) ? " checked=\"yes\"" : '';

        return "<p {$wrapperAttributes}>" .
            "<label>" .
                "<input type=\"checkbox\" name=\"{$this->name}\"{$inputAttributes} value=\"true\"{$selected}>" .
                "<span class=\"label\">{$label}{$marker}</span>" .
            "</label>" .
            $errorMessage .
        "</p>\n";
    }
    // }}}

    // {{{ validate()
    /**
     * @brief validates boolean input element value
     *
     * Overrides input::validate(). Checks if the value of the current input
     * element is valid according to it's validator object. In case of boolean
     * the value has to be true if field is required.
     * 
     * @return $this->valid (bool) validation result
     **/
    public function validate() {
        if (!$this->validated) {
            $this->validated = true;

            $this->valid = (($this->value !== null)
                && ($this->validator->validate($this->value) || $this->isEmpty())
                && ($this->value || !$this->required)
            );
        }

        return $this->valid;
    }
    // }}}

    // {{{ setValue()
    /**
     * @brief   set the boolean element value
     *
     * Sets the current input elements' value. Converts it to boolean if
     * necessary.
     *
     * @param   $newValue       (mixed) new element value
     * @return  $this->value    (bool)  converted value
     **/
    public function setValue($newValue) {
        if (is_bool($newValue)) {
            $this->value = $newValue;
        } else if ($newValue === "true") {
            $this->value = true;
        } else {
            $this->value = false;
        }

        return $this->value;
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
