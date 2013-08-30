<?php
/**
 * @file    address.php
 * @brief   address fieldset element
 *
 * @author Ben Wallis <benedict_wallis@yahoo.co.uk>
 **/

namespace depage\htmlform\elements;

/**
 * @brief Default address fieldset.
 *
 * Class to get user address information. It generates a fieldset
 * that consists of
 *
 *     - a state
 *     - a country select
 *     
 * @section usage
 *
 * @code
 * <?php 
 *     $form = new depage\htmlform\htmlform('myform');
 *
 *     // add a creditcard fieldset
 *     $form->addAddress('address', array(
 *         'label' => 'Address',
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
class address extends fieldset {
    // {{{ __construct()
    /**
     * @brief   multiple class constructor
     *
     * @param   $name       (string)    element name
     * @param   $parameters (array)     element parameters, HTML attributes, validator specs etc.
     * @param   $form       (object)    parent form object
     * @return  void
     **/
    public function __construct($name, $parameters, $form) {
        parent::__construct($name, $parameters, $form);
        
        if (isset($parameters['defaultAddress1'])) {
            $this->defaults['defaultAddress1'] = $parameters['defaultAddress1'];
        }
        
        if (isset($parameters['defaultAddress2'])) {
            $this->defaults['defaultAddress2'] = $parameters['defaultAddress2'];
        }
        
        if (isset($parameters['defaultCity'])) {
            $this->defaults['defaultCity'] = $parameters['defaultCity'];
        }
        
        if (isset($parameters['defaultState'])) {
            $this->defaults['defaultState'] = $parameters['defaultState'];
        }
        
        if (isset($parameters['defaultCountry'])) {
            $this->defaults['defaultCountry'] = $parameters['defaultCountry'];
        }
        
        if (isset($parameters['defaultZip'])) {
            $this->defaults['defaultZip'] = $parameters['defaultZip'];
        }
        
        $this->defaults['priorityCountries'] = isset($parameters['priorityCountries'])
            ? $parameters['priorityCountries']
            : array();
        
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
    protected function setDefaults() {
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
     * @return  void
     **/
    public function addChildElements() {
        parent::addChildElements();
        
        if(isset($this->defaults['defaultAddress1'])) {
            $this->addText($this->prefix . "line_1", array(
                'label' => $this->labelAddress1,
                'defaultValue' => $this->defaults['defaultAddress1'],
                'required' => $this->required,
            ));
        }
        
        if(isset($this->defaults['defaultAddress2'])) {
            $this->addText($this->prefix . "line_2", array(
                'label' => $this->labelAddress2,
                'defaultValue' => $this->defaults['defaultAddress2'],
                'required' => $this->required,
            ));
        }
        
        if(isset($this->defaults['defaultCountry'])) {
            $this->addCountry($this->prefix . "country", array(
                'label' => $this->labelCountry,
                'priorityCountries' => $this->defaults['priorityCountries'],
                'defaultValue' => $this->defaults['defaultCountry'],
                'required' => $this->required,
            ));
        }
        
        if(isset($this->defaults['defaultState'])) {
            $this->addState($this->prefix . "state", array(
                'label' => $this->labelState,
                'defaultValue' => $this->defaults['defaultState'],
                'required' => $this->required,
            ));
        }
        
        if(isset($this->defaults['defaultCity'])) {
            $this->addText($this->prefix . "city", array(
                'label' => $this->labelCity,
                'defaultValue' => $this->defaults['defaultCity'],
                'required' => $this->required,
            ));
        }
        
        if(isset($this->defaults['defaultZip'])) {
            $this->addText($this->prefix . "zip", array(
                'label' => $this->labelZip,
                'defaultValue' => $this->defaults['defaultZip'],
                'required' => $this->required,
            ));
        }
    }
    // }}}
    
    // {{{ validate()
    /**
     * @brief   Validate the address data
     *
     * @return  (bool) validation result
     **/
    public function validate() {
        if(parent::validate() && isset($this->defaultCountry) && isset($this->defaultState)) {
            // check selected state matches the country
            $country_states = state::getStates();
            $country = $this->getElement("{$this->name}_country");
            $state = $this->getElement("{$this->name}_state");
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
