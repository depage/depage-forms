<?php

/**
 * @file    address.php
 * @brief   address fieldset element
 *
 * @author Ben Wallis <benedict_wallis@yahoo.co.uk>
 **/

namespace Depage\HtmlForm\Elements;

/**
 * @brief Default address fieldset.
 *
 * Class to get user address information. It generates a fieldset
 * that consists of
 *
 * - a state
 * - a country select
 *
 * Usage
 * -----
 *
 * @code
    <?php
        $form = new Depage\HtmlForm\HtmlForm('myform');

        // add a creditcard fieldset
        $form->addAddress('address', array(
            'label' => 'Address',
        ));

        // process form
        $form->process();

        // Display the form.
        echo ($form);
    @endcode
 **/
class Address extends Fieldset
{
    // {{{ __construct()
    /**
     * @brief   multiple class constructor
     *
     * @param  string $name       element name
     * @param  array  $parameters element parameters, HTML attributes, validator specs etc.
     * @param  object $form       parent form object
     * @return void
     **/
    public function __construct(string $name, array $parameters, object $form)
    {
        parent::__construct($name, $parameters, $form);

        $this->props = [
            'Address1',
            'Address2',
            'Zip',
            'City',
            'Country',
            'State',
        ];
        $this->propsMaxLength = [
            'Address1' => 255,
            'Address2' => 255,
            'Zip' => 45,
            'City' => 255,
            'VatId' => 60,
        ];


        if (isset($parameters['props'])) {
            $this->props = $parameters['props'];
        }
        foreach ($this->props as $prop) {
            if (isset($parameters["default$prop"])) {
                $this->defaults["default$prop"] = $parameters["default$prop"];
            }
        }

        $this->defaults['priorityCountries'] = isset($parameters['priorityCountries'])
            ? $parameters['priorityCountries']
            : [];

        $this->prefix = isset($parameters['prefix'])
            ? rtrim($parameters['prefix'], '_') . '_'
            : '';
    }
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

        $this->defaults['required']             = false;
        $this->defaults['labelAddress1']        = _("Address 1");
        $this->defaults['labelAddress2']        = _("Address 2");
        $this->defaults['labelCity']            = _("City");
        $this->defaults['labelState']           = _("State");
        $this->defaults['labelZip']             = _("Zip");
        $this->defaults['labelCountry']         = _("Country");
    }
    // }}}

    // {{{ addChildElements()
    /**
     * @brief   adds address-inputs to fieldset
     *
     * @return void
     **/
    public function addChildElements(): void
    {
        parent::addChildElements();

        foreach ($this->props as $prop) {
            if (isset($this->defaults["default$prop"])) {
                $labelVar = "label$prop";

                if ($prop == "State") {
                    $this->addState($this->prefix . "state", [
                        'label' => $this->labelState,
                        'defaultValue' => $this->defaults['defaultState'],
                        //'required' => $this->required,
                    ]);
                } elseif ($prop == "Country") {
                    $this->addCountry($this->prefix . "country", [
                        'label' => $this->labelCountry,
                        'priorityCountries' => $this->defaults['priorityCountries'],
                        'defaultValue' => $this->defaults['defaultCountry'],
                        'required' => $this->required,
                    ]);
                } else {
                    $this->addText($this->prefix . strtolower($prop), [
                        'label' => $this->$labelVar,
                        'maxlength' => $this->propsMaxLength[$prop],
                        'defaultValue' => $this->defaults["default$prop"],
                        'required' => $prop != "Address2" && $this->required,
                    ]);
                }
            }
        }
    }
    // }}}

    // {{{ validate()
    /**
     * @brief   Validate the address data
     *
     * @return bool validation result
     **/
    public function validate(): bool
    {
        if (parent::validate() && isset($this->defaultCountry) && isset($this->defaultState)) {
            // check selected state matches the country
            $country_states = state::getStates();
            $country = $this->getElement("{$this->prefix}country");
            $state = $this->getElement("{$this->prefix}state");
            $this->valid = isset($country_states[$country->getValue()][$state->getValue()]);
            if (!$this->valid) {
                $state->errorMessage = _("State and Country do not match");
                $state->valid = false;
            }

            // TODO validate zip code
        }

        return $this->valid;
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
