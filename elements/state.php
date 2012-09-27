<?php
/**
 * @file    state.php
 * @brief   state select input element
 *
 * @author Ben Wallis <benedict_wallis@yahoo.co.uk>
 **/

namespace depage\htmlform\elements;

use depage\htmlform\abstracts;

/**
 * @brief HTML-multiple-choice States input select .
 *
 * Class for generic states select list.
 *
 * Option values are 4-digit alpha ISO state codes.
 * 
 * @section usage
 *
 * @code
 * <?php
 *     $form = new depage\htmlform\htmlform('myform');
 *
 *     // add states
 *     $form->addState('state');
 *
 *     // process form
 *     $form->process();
 *
 *     // Display the form.
 *     echo ($form);
 * ?>
 * @endcode
 **/
class state extends single {
    // {{{ variables
    /**
    * @brief Contains list of ISO countries.
    **/
    protected $list = array();
    // }}}
    
    // {{{ variables
    /**
     * @brief Gets the Default States list
     * 
     * @param $iso - list of state iso codes to filter 
     **/
    public static function getStates($iso = null) {
        
        $country_states = array(
            // http://en.wikipedia.org/wiki/ISO_3166-2:DE
            'de' => array(
                'de-bw' => _("Baden-Württemberg"),
                'de-by' => _("Bayern"),
                'de-be' => _("Berlin"),
                'de-bb' => _("Brandenburg"),
                'de-hb' => _("Bremen"),
                'de-hh' => _("Hamburg"),
                'de-he' => _("Hessen"),
                'de-mv' => _("Mecklenburg-Vorpommern"),
                'de-ni' => _("Niedersachsen"),
                'de-nw' => _("Nordrhein-Westfalen"),
                'de-rp' => _("Rheinland-Pfalz"),
                'de-sl' => _("Saarland"),
                'de-sn' => _("Sachsen"),
                'de-st' => _("Sachsen-Anhalt"),
                'de-sh' => _("Schleswig-Holstein"),
                'de-th' => _("Thüringen")
            ),
            // http://en.wikipedia.org/wiki/ISO_3166-2:US
            'us' => array(
                'us-al' => _("Alabama"),
                'us-ak' => _("Alaska"),
                'us-az' => _("Arizona"),
                'us-ar' => _("Arkansas"),
                'us-ca' => _("California"),
                'us-co' => _("Colorado"),
                'us-ct' => _("Connecticut"),
                'us-de' => _("Delaware"),
                'us-fl' => _("Florida"),
                'us-ga' => _("Georgia"),
                'us-hi' => _("Hawaii"),
                'us-id' => _("Idaho"),
                'us-il' => _("Illinois"),
                'us-in' => _("Indiana"),
                'us-ia' => _("Iowa"),
                'us-ks' => _("Kansas"),
                'us-ky' => _("Kentucky"),
                'us-la' => _("Louisiana"),
                'us-me' => _("Maine"),
                'us-md' => _("Maryland"),
                'us-ma' => _("Massachusetts"),
                'us-mi' => _("Michigan"),
                'us-mn' => _("Minnesota"),
                'us-ms' => _("Mississippi"),
                'us-mo' => _("Missouri"),
                'us-mt' => _("Montana"),
                'us-ne' => _("Nebraska"),
                'us-nv' => _("Nevada"),
                'us-nh' => _("New Hampshire"),
                'us-nj' => _("New Jersey"),
                'us-nm' => _("New Mexico"),
                'us-ny' => _("New York"),
                'us-nc' => _("North Carolina"),
                'us-nd' => _("North Dakota"),
                'us-oh' => _("Ohio"),
                'us-ok' => _("Oklahoma"),
                'us-or' => _("Oregon"),
                'us-pa' => _("Pennsylvania"),
                'us-ri' => _("Rhode Island"),
                'us-sc' => _("South Carolina"),
                'us-sd' => _("South Dakota"),
                'us-tn' => _("Tennessee"),
                'us-tx' => _("Texas"),
                'us-ut' => _("Utah"),
                'us-vt' => _("Vermont"),
                'us-va' => _("Virginia"),
                'us-wa' => _("Washington"),
                'us-wv' => _("West Virginia"),
                'us-wi' => _("Wisconsin"),
                'us-wy' => _("Wyoming"),
                'us-dc' => _("District of Columbia"),
                'us-as' => _("American Samoa"),
                'us-gu' => _("Guam"),
                'us-mp' => _("Northern Mariana Islands"),
                'us-pr' => _("Puerto Rico"),
                'us-um' => _("United States Minor Outlying Islands"),
                'us-vi' => _("Virgin Islands, U.S.)")
            ),
        );
        
        // return a subset
        if ($iso !== null){
            // search for iso state codes
            foreach($country_states as $country => &$states) {
                if(isset($states[$iso])){
                    return $states[$iso];
                }
            }
            
            return '';
        }
        
        return $country_states;
    }
    // }}}
    
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
        
        if (isset($parameters['defaultValue'])) {
            $this->defaults['defaultValue'] = $parameters['defaultValue'];
        }
        
        $this->list = isset($parameters['states'])
            ? $parameters['states']
            : self::getStates();
        
        // make sure all keys are lower case
        $this->list = array_change_key_case($this->list, CASE_LOWER);
        
        // sort alphabetically
        asort($this->list);
        
        $this->list = array('' => _("Please Select")) + $this->list;
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
    * @return  void
    **/
    protected function setDefaults() {
        parent::setDefaults();
        
        $this->defaults['skin'] = 'select';
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
