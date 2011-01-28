<?php 

require_once('elementFieldset.php');

/**
 * The fieldset class holds HTML-fieldset specific attributes and methods.
 **/
class creditcard extends elementFieldset {
    /**
     * adds creditcard-inputs to fieldset
     *
     * @todo check regular expressions based on (?): http://www.regular-expressions.info/creditcard.html
     *
     * @return void
     **/
    protected function addChildElements() {
        parent::addChildElements();

        $cardnames = array(
            'visa' => "Visa",
            'americanexpress' => "American Express",
            'mastercard' => "MasterCard",
        );
        $options = array();
        foreach ($this->cardtypes as $card) {
            if (isset($cardnames[$card])) {
                $options[$card] = $cardnames[$card];
            }
        }

        $this->addSelect($this->name . "_card_type", array(
            'label' => "",
            'optionList' => $options,
        ));
        $this->addText($this->name . "_card_number", array(
            'label' => $this->labelNumber,
            'required' => true,
            'validator' => "/^(?:\d[ -]*?){13,16}$/",
        ));
        $this->addText($this->name . "_card_numbercheck", array(
            'label' => $this->labelCheck,
            'required' => true,
            'validator' => "/^\d{3,4}$/",
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
     * Validate the creditcard data
     *
     * @todo validate based on (?): http://www-sst.informatik.tu-cottbus.de/~db/doc/Java/GalileoComputing-JavaInsel/java-04.htm#t321
     * @todo validate specific to cardtype
     *
     * @return void
     **/
    public function validate() {
        parent::validate();
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
        $this->defaults['cardtypes'] = array(
            "visa",
            "americanexpress",
            "mastercard",
        );
    }
}
