<?php 

require_once('fieldset.php');

/**
 * The fieldset class holds HTML-fieldset specific attributes and methods.
 **/
class creditcard extends fieldset {
    /**
     * overwritable method to add child elements
     *
     *  @return void
     **/
    protected function addChildElements() {
        parent::addChildElements();

        $this->addSelect($this->name . "_card_type", array(
            'label' => "",
            'optionList' => array(
                'visa' => "Visa",
                'americanexpress' => "American Express",
                'mastercard' => "MasterCard",
            ),
        ));
        $this->addText($this->name . "_card_number", array(
            'label' => $this->labelNumber,
            'required' => true,
            'validator' => "/^(?:\d[ -]*?){13,16}$/",
        ));
        $this->addText($this->name . "_card_numbercheck", array(
            'label' => $this->labelCheck,
            'required' => true,
            'validator' => "/^(?:\d[ -]*?){3}$/",
        ));
        $this->addText($this->name . "_card_expirydate", array(
            'label' => $this->labelExpirationDate,
            'required' => true,
            'validator' => "/^\d{2}\/\d{2}$/",
        ));
        $this->addText($this->name . "_card_owner", array(
            'label' => $this->labelOwner,
            'required' => true,
        ));
    }

    /**
     * Specifies default values of fieldset attributes for the parent class
     * constructor.
     *
     * @return void
     **/
    protected function setDefaults() {
        parent::setDefauls();

        $this->defaults['label'] = $this->name;
        $this->defaults['labelNumber'] = "Creditcard Number";
        $this->defaults['labelCheck'] = "CVV/CVC";
        $this->defaults['labelExpirationDate'] = "Expiration Date MM/YY";
        $this->defaults['labelOwner'] = "Card Owner";
    }


}
