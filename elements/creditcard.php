<?php 

namespace depage\htmlform\elements;

/**
 * The fieldset class holds HTML-fieldset specific attributes and methods.
 **/
class creditcard extends fieldset {
    public function __construct($name, $parameters = array()) {
        parent::__construct($name, $parameters);

        $this->labelNumber          = (isset($parameters['labelnumber']))           ? $parameters['labelnumber']            : "Creditcard Number";
        $this->labelCheck           = (isset($parameters['labelcheck']))            ? $parameters['labelcheck']             : "CVV/CVC";
        $this->labelExpirationDate  = (isset($parameters['labelexpirationdate']))   ? $parameters['labelexpirationdate']    : "Expiration Date MM/YY";
        $this->labelOwner           = (isset($parameters['labelowner']))            ? $parameters['labelowner']             : "Card Owner";
        $this->cardtypes            = (isset($parameters['cardtypes']))             ? $parameters['cardtypes']              : array(
            "visa",
            "americanexpress",
            "mastercard",
        );

    }
    
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

        $this->addSingle($this->name . "_card_type", array(
            'label' => "",
            'list' => $options,
            'skin' => 'select',
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
}
