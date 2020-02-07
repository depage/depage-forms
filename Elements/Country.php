<?php
/**
 * @file    country.php
 * @brief   country select input element
 *
 * @author Ben Wallis <benedict_wallis@yahoo.co.uk>
 **/

namespace Depage\HtmlForm\Elements;

/**
 * @brief HTML-multiple-choice country input select .
 *
 * Class for generic country select list.
 *
 * Option values are 2-digit alpha ISO country codes.
 * Country names are run through get text and sorted alphabetically.
 *
 * An optional 'priority' parameter moves countries to the top of the select based on the DEPAGE_LANG locale:
 * e.g. 'de' => array('de','at'), 'en' => array('au','gb','ca','us')
 *
 * Usage
 * -----
 *
 * @code
    <?php
        $form = new Depage\HtmlForm\HtmlForm('myform');

        // add country
        $form->addCountry('country');

        // process form
        $form->process();

        // Display the form.
        echo ($form);
    @endcode
 **/
class Country extends Single
{
    // {{{ variables
    /**
    * @brief Contains list of ISO countries.
    **/
    protected $list = array();
    // }}}

    // {{{ getCountries
    /**
     * @brief Gets the Default Countries list
     *
     * @param array $iso list/subset of country iso codes to filter
     **/
    public static function getCountries($iso = null)
    {
        $countries = array(
            'ad' => _("Andorra"),
            'ae' => _("United Arab Emirates"),
            'af' => _("Afghanistan"),
            'ag' => _("Antigua &amp; Barbuda"),
            'ai' => _("Anguilla"),
            'al' => _("Albania"),
            'am' => _("Armenia"),
            'an' => _("Netherlands Antilles"),
            'ao' => _("Angola"),
            'aq' => _("Antarctica"),
            'ar' => _("Argentina"),
            'as' => _("American Samoa"),
            'at' => _("Austria"),
            'au' => _("Australia"),
            'aw' => _("Aruba"),
            'az' => _("Azerbaijan"),
            'ba' => _("Bosnia and Herzegovina"),
            'bb' => _("Barbados"),
            'bd' => _("Bangladesh"),
            'be' => _("Belgium"),
            'bf' => _("Burkina Faso"),
            'bg' => _("Bulgaria"),
            'bh' => _("Bahrain"),
            'bi' => _("Burundi"),
            'bj' => _("Benin"),
            'bm' => _("Bermuda"),
            'bn' => _("Brunei Darussalam"),
            'bo' => _("Bolivia"),
            'br' => _("Brazil"),
            'bs' => _("Bahama"),
            'bt' => _("Bhutan"),
            'bv' => _("Bouvet Island"),
            'bw' => _("Botswana"),
            'by' => _("Belarus"),
            'bz' => _("Belize"),
            'ca' => _("Canada"),
            'cc' => _("Cocos (Keeling) Islands"),
            'cf' => _("Central African Republic"),
            'cg' => _("Congo"),
            'ch' => _("Switzerland"),
            'ci' => _("Côte D\'ivoire (Ivory Coast)"),
            'ck' => _("Cook Iislands"),
            'cl' => _("Chile"),
            'cm' => _("Cameroon"),
            'cn' => _("China"),
            'co' => _("Colombia"),
            'cr' => _("Costa Rica"),
            'cu' => _("Cuba"),
            'cv' => _("Cape Verde"),
            'cx' => _("Christmas Island"),
            'cy' => _("Cyprus"),
            'cz' => _("Czech Republic"),
            'de' => _("Germany"),
            'dj' => _("Djibouti"),
            'dk' => _("Denmark"),
            'dm' => _("Dominica"),
            'do' => _("Dominican Republic"),
            'dz' => _("Algeria"),
            'ec' => _("Ecuador"),
            'ee' => _("Estonia"),
            'eg' => _("Egypt"),
            'eh' => _("Western Sahara"),
            'er' => _("Eritrea"),
            'es' => _("Spain"),
            'et' => _("Ethiopia"),
            'fi' => _("Finland"),
            'fj' => _("Fiji"),
            'fk' => _("Falkland Islands"),
            'fm' => _("Micronesia"),
            'fo' => _("Faroe Islands"),
            'fr' => _("France"),
            'fx' => _("France, Metropolitan"),
            'ga' => _("Gabon"),
            'gb' => _("United Kingdom"),
            'gd' => _("Grenada"),
            'ge' => _("Georgia"),
            'gf' => _("French Guiana"),
            'gh' => _("Ghana"),
            'gi' => _("Gibraltar"),
            'gl' => _("Greenland"),
            'gm' => _("Gambia"),
            'gn' => _("Guinea"),
            'gp' => _("Guadeloupe"),
            'gq' => _("Equatorial Guinea"),
            'gr' => _("Greece"),
            'gs' => _("South Georgia and the South Sandwich Islands"),
            'gt' => _("Guatemala"),
            'gu' => _("Guam"),
            'gw' => _("Guinea-Bissau"),
            'gy' => _("Guyana"),
            'hk' => _("Hong Kong"),
            'hm' => _("Heard &amp; McDonald Islands"),
            'hn' => _("Honduras"),
            'hr' => _("Croatia"),
            'ht' => _("Haiti"),
            'hu' => _("Hungary"),
            'id' => _("Indonesia"),
            'ie' => _("Ireland"),
            'il' => _("Israel"),
            'in' => _("India"),
            'io' => _("British Indian Ocean Territory"),
            'iq' => _("Iraq"),
            'ir' => _("Islamic Republic of Iran"),
            'is' => _("Iceland"),
            'it' => _("Italy"),
            'jm' => _("Jamaica"),
            'jo' => _("Jordan"),
            'jp' => _("Japan"),
            'ke' => _("Kenya"),
            'kg' => _("Kyrgyzstan"),
            'kh' => _("Cambodia"),
            'ki' => _("Kiribati"),
            'km' => _("Comoros"),
            'kn' => _("St. Kitts and Nevis"),
            'kp' => _("Korea, Democratic People\'s Republic of"),
            'kr' => _("Korea, Republic of"),
            'kw' => _("Kuwait"),
            'ky' => _("Cayman Islands"),
            'kz' => _("Kazakhstan"),
            'la' => _("Lao People\'s Democratic Republic"),
            'lb' => _("Lebanon"),
            'lc' => _("Saint Lucia"),
            'li' => _("Liechtenstein"),
            'lk' => _("Sri Lanka"),
            'lr' => _("Liberia"),
            'ls' => _("Lesotho"),
            'lt' => _("Lithuania"),
            'lu' => _("Luxembourg"),
            'lv' => _("Latvia"),
            'ly' => _("Libyan Arab Jamahiriya"),
            'ma' => _("Morocco"),
            'mc' => _("Monaco"),
            'md' => _("Moldova, Republic of"),
            'mg' => _("Madagascar"),
            'mh' => _("Marshall Islands"),
            'ml' => _("Mali"),
            'mn' => _("Mongolia"),
            'mm' => _("Myanmar"),
            'mo' => _("Macau"),
            'mp' => _("Northern Mariana Islands"),
            'mq' => _("Martinique"),
            'mr' => _("Mauritania"),
            'ms' => _("Monserrat"),
            'mt' => _("Malta"),
            'mu' => _("Mauritius"),
            'mv' => _("Maldives"),
            'mw' => _("Malawi"),
            'mx' => _("Mexico"),
            'my' => _("Malaysia"),
            'mz' => _("Mozambique"),
            'na' => _("Namibia"),
            'nc' => _("New Caledonia"),
            'ne' => _("Niger"),
            'nf' => _("Norfolk Island"),
            'ng' => _("Nigeria"),
            'ni' => _("Nicaragua"),
            'nl' => _("Netherlands"),
            'no' => _("Norway"),
            'np' => _("Nepal"),
            'nr' => _("Nauru"),
            'nu' => _("Niue"),
            'nz' => _("New Zealand"),
            'om' => _("Oman"),
            'pa' => _("Panama"),
            'pe' => _("Peru"),
            'pf' => _("French Polynesia"),
            'pg' => _("Papua New Guinea"),
            'ph' => _("Philippines"),
            'pk' => _("Pakistan"),
            'pl' => _("Poland"),
            'pm' => _("St. Pierre &amp; Miquelon"),
            'pn' => _("Pitcairn"),
            'pr' => _("Puerto Rico"),
            'pt' => _("Portugal"),
            'pw' => _("Palau"),
            'py' => _("Paraguay"),
            'qa' => _("Qatar"),
            're' => _("Réunion"),
            'ro' => _("Romania"),
            'ru' => _("Russian Federation"),
            'rw' => _("Rwanda"),
            'sa' => _("Saudi Arabia"),
            'sb' => _("Solomon Islands"),
            'sc' => _("Seychelles"),
            'sd' => _("Sudan"),
            'se' => _("Sweden"),
            'sg' => _("Singapore"),
            'sh' => _("St. Helena"),
            'si' => _("Slovenia"),
            'sj' => _("Svalbard &amp; Jan Mayen Islands"),
            'sk' => _("Slovakia"),
            'sl' => _("Sierra Leone"),
            'sm' => _("San Marino"),
            'sn' => _("Senegal"),
            'so' => _("Somalia"),
            'sr' => _("Suriname"),
            'st' => _("Sao Tome &amp; Principe"),
            'sv' => _("El Salvador"),
            'sy' => _("Syrian Arab Republic"),
            'sz' => _("Swaziland"),
            'tc' => _("Turks &amp; Caicos Islands"),
            'td' => _("Chad"),
            'tf' => _("French Southern Territories"),
            'tg' => _("Togo"),
            'th' => _("Thailand"),
            'tj' => _("Tajikistan"),
            'tk' => _("Tokelau"),
            'tm' => _("Turkmenistan"),
            'tn' => _("Tunisia"),
            'to' => _("Tonga"),
            'tp' => _("East Timor"),
            'tr' => _("Turkey"),
            'tt' => _("Trinidad &amp; Tobago"),
            'tv' => _("Tuvalu"),
            'tw' => _("Taiwan, Province of China"),
            'tz' => _("Tanzania, United Republic of"),
            'ua' => _("Ukraine"),
            'ug' => _("Uganda"),
            'um' => _("United States Minor Outlying Islands"),
            'us' => _("United States of America"),
            'uy' => _("Uruguay"),
            'uz' => _("Uzbekistan"),
            'va' => _("Vatican City State (Holy See)"),
            'vc' => _("St. Vincent &amp; the Grenadines"),
            've' => _("Venezuela"),
            'vg' => _("British Virgin Islands"),
            'vi' => _("United States Virgin Islands"),
            'vn' => _("Viet Nam"),
            'vu' => _("Vanuatu"),
            'wf' => _("Wallis &amp; Futuna Islands"),
            'ws' => _("Samoa"),
            'ye' => _("Yemen"),
            'yt' => _("Mayotte"),
            'yu' => _("Yugoslavia"),
            'za' => _("South Africa"),
            'zm' => _("Zambia"),
            'zr' => _("Zaire"),
            'zw' => _("Zimbabwe")
        );

        // return a subset
        if ($iso !== null) {
            if (is_array($iso)) {
                return array_intersect_key($countries, $iso);
            } else {
                return isset($countries[$iso]) ? $countries[$iso] : '';
            }
        }

        return $countries;
    }
    // }}}

    // {{{ __construct()
    /**
    * @brief   multiple class constructor
    *
    * @param   string   $name       element name
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

        if (isset($parameters['priorityCountries'])) {
            $this->defaults['priorityCountries'] = $parameters['priorityCountries'];
        }

        $this->list = isset($parameters['countries'])
            ? $parameters['countries']
            : self::getCountries();

        // make sure all keys are lower case
        $this->list = array_change_key_case($this->list, CASE_LOWER);

        // sort alphabetically
        asort($this->list);

        // move priority countries to the top based on options and language
        if (defined("DEPAGE_LANG") && isset($parameters['priorityCountries'][DEPAGE_LANG])) {
            $priorityCountries = array_reverse($parameters['priorityCountries'][DEPAGE_LANG], true);
            // make sure all keys are lower case to match countries
            $priorityCountries = array_change_key_case($priorityCountries, CASE_LOWER);

            foreach ($priorityCountries as &$country_code) {
                if (isset($this->list[$country_code])) {
                    $top = array($country_code => $this->list[$country_code]);
                    unset($this->list[$country_code]);
                    $this->list = $top + $this->list;
                }
            }
        }

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
