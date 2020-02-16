<?php
/**
 * @file    state.php
 * @brief   state select input element
 *
 * @author Ben Wallis <benedict_wallis@yahoo.co.uk>
 **/

namespace Depage\HtmlForm\Elements;

/**
 * @brief HTML-multiple-choice States input select .
 *
 * Class for generic states select list.
 *
 * Option values are 4-digit alpha ISO state codes.
 *
 * Usage
 * -----
 *
 * @code
    <?php
        $form = new Depage\HtmlForm\HtmlForm('myform');

        // add states
        $form->addState('state');

        // process form
        $form->process();

        // Display the form.
        echo ($form);
    @endcode
 **/
class State extends Single
{
    // {{{ variables
    /**
    * @brief Contains list of ISO countries.
    **/
    protected $list = array();
    // }}}

    // {{{ getStates()
    /**
     * @brief Gets the Default States list
     *
     * @param array $iso list/subset of state iso codes to filter
     **/
    public static function getStates($iso = null)
    {
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
            'ca' => array(
                'ca-ab' => _("Alberta"),
                'ca-bc' => _("British Columbia"),
                'ca-mb' => _("Manitoba"),
                'ca-nb' => _("New Brunswick"),
                'ca-nl' => _("Newfoundland and Labrador"),
                'ca-nt' => _("Northwest Territories"),
                'ca-ns' => _("Nova Scotia"),
                'ca-nu' => _("Nunavut"),
                'ca-on' => _("Ontario"),
                'ca-pe' => _("Prince Edward Island"),
                'ca-qc' => _("Quebec"),
                'ca-sk' => _("Saskatchewan"),
                'ca-yt' => _("Yukon"),
            ),
            'it' => array(
                'it-ag' => _("Agrigento"),
                'it-al' => _("Alessandria"),
                'it-an' => _("Ancona"),
                'it-ao' => _("Aosta"),
                'it-ar' => _("Arezzo"),
                'it-ap' => _("Ascoli Piceno"),
                'it-at' => _("Asti"),
                'it-av' => _("Avellino"),
                'it-ba' => _("Bari"),
                'it-bt' => _("Barletta-Andria-Trani"),
                'it-bl' => _("Belluno"),
                'it-bn' => _("Benevento"),
                'it-bg' => _("Bergamo"),
                'it-bi' => _("Biella"),
                'it-bo' => _("Bologna"),
                'it-bz' => _("Bolzano"),
                'it-bs' => _("Brescia"),
                'it-br' => _("Brindisi"),
                'it-ca' => _("Cagliari"),
                'it-cl' => _("Caltanissetta"),
                'it-cb' => _("Campobasso"),
                'it-ci' => _("Carbonia-Iglesias"),
                'it-ce' => _("Caserta"),
                'it-ct' => _("Catania"),
                'it-cz' => _("Catanzaro"),
                'it-ch' => _("Chieti"),
                'it-co' => _("Como"),
                'it-cs' => _("Cosenza"),
                'it-cr' => _("Cremona"),
                'it-kr' => _("Crotone"),
                'it-cn' => _("Cuneo"),
                'it-en' => _("Enna"),
                'it-fm' => _("Fermo"),
                'it-fe' => _("Ferrara"),
                'it-fi' => _("Firenze"),
                'it-fg' => _("Foggia"),
                'it-fc' => _("Forlì-Cesena"),
                'it-fr' => _("Frosinone"),
                'it-ge' => _("Genova"),
                'it-go' => _("Gorizia"),
                'it-gr' => _("Grosseto"),
                'it-im' => _("Imperia"),
                'it-is' => _("Isernia"),
                'it-aq' => _("L'Aquila"),
                'it-sp' => _("La Spezia"),
                'it-lt' => _("Latina"),
                'it-le' => _("Lecce"),
                'it-lc' => _("Lecco"),
                'it-li' => _("Livorno"),
                'it-lo' => _("Lodi"),
                'it-lu' => _("Lucca"),
                'it-mc' => _("Macerata"),
                'it-mn' => _("Mantova"),
                'it-ms' => _("Massa-Carrara"),
                'it-mt' => _("Matera"),
                'it-vs' => _("Medio Campidano"),
                'it-me' => _("Messina"),
                'it-mi' => _("Milano"),
                'it-mo' => _("Modena"),
                'it-mb' => _("Monza e della Brianza"),
                'it-na' => _("Napoli"),
                'it-no' => _("Novara"),
                'it-nu' => _("Nuoro"),
                'it-og' => _("Ogliastra"),
                'it-ot' => _("Olbia-Tempio"),
                'it-or' => _("Oristano"),
                'it-pd' => _("Padova"),
                'it-pa' => _("Palermo"),
                'it-pr' => _("Parma"),
                'it-pv' => _("Pavia"),
                'it-pg' => _("Perugia"),
                'it-pu' => _("Pesaro e Urbino"),
                'it-pe' => _("Pescara"),
                'it-pc' => _("Piacenza"),
                'it-pi' => _("Pisa"),
                'it-pt' => _("Pistoia"),
                'it-pn' => _("Pordenone"),
                'it-pz' => _("Potenza"),
                'it-po' => _("Prato"),
                'it-rg' => _("Ragusa"),
                'it-ra' => _("Ravenna"),
                'it-rc' => _("Reggio Calabria"),
                'it-re' => _("Reggio Emilia"),
                'it-ri' => _("Rieti"),
                'it-rn' => _("Rimini"),
                'it-rm' => _("Roma"),
                'it-ro' => _("Rovigo"),
                'it-sa' => _("Salerno"),
                'it-ss' => _("Sassari"),
                'it-sv' => _("Savona"),
                'it-si' => _("Siena"),
                'it-sr' => _("Siracusa"),
                'it-so' => _("Sondrio"),
                'it-ta' => _("Taranto"),
                'it-te' => _("Teramo"),
                'it-tr' => _("Terni"),
                'it-to' => _("Torino"),
                'it-tp' => _("Trapani"),
                'it-tn' => _("Trento"),
                'it-tv' => _("Treviso"),
                'it-ts' => _("Trieste"),
                'it-ud' => _("Udine"),
                'it-va' => _("Varese"),
                'it-ve' => _("Venezia"),
                'it-vb' => _("Verbano-Cusio-Ossola"),
                'it-vc' => _("Vercelli"),
                'it-vr' => _("Verona"),
                'it-vv' => _("Vibo Valentia"),
                'it-vi' => _("Vicenza"),
                'it-vt' => _("Viterbo"),
            ),
        );

        // return a subset
        if ($iso !== null) {
            // search for iso state codes
            if (is_string($iso) && isset($country_states[$iso])) {
                return $country_states[$iso];
            } else if (is_array($iso)) {
                $toDelete = array_diff(array_keys($country_states), $iso);
                foreach($toDelete as $country) {
                    unset($country_states[$country]);
                }
                return $country_states;
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
    * @param   strin    $name       element name
    * @param   array    $parameters element parameters, HTML attributes, validator specs etc.
    * @param   object   $form       parent form object
    * @return  void
    **/
    public function __construct($name, $parameters, $form)
    {
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
    protected function setDefaults()
    {
        parent::setDefaults();

        $this->defaults['skin'] = 'select';
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
