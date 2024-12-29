<?php

/**
 * @file    creditcard.php
 * @brief   creditcard fieldset element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace Depage\HtmlForm\Elements;

/**
 * @brief Default creditcard fieldset.
 *
 * Class to get a user creditcard information. It generates a fieldset
 * that consists of
 *
 * - a creditcard-type (like 'Visa', 'MasterCard' or 'American Express')
 * - a field for the creditcard-number
 * - a field for the expiry-date
 * - a field for the card-owner
 *
 * At the moment this input only checks the formatting of the numbers but not, if
 * it is a valid creditcard.
 *
 * Usage
 * -----
 *
 * @code
    <?php
        $form = new Depage\HtmlForm\HtmlForm('myform');

        // add a creditcard fieldset
        $form->addCreditcard('creditcard', array(
            'label' => 'Credit Card',
        ));

        // process form
        $form->process();

        // Display the form.
        echo ($form);
    @endcode
 **/
class Creditcard extends Fieldset
{
    // {{{ variables
    /**
     * @brief   label for the creditcard-number field
     **/
    protected $labelNumber;

    /**
     * @brief   label for the creditcard-check field
     **/
    protected $labelCheck;

    /**
     * @brief   label for the creditcard-expirydate field
     **/
    protected $labelExpirationDate;

    /**
     * @brief   label for the creditcard-owner field
     **/
    protected $labelOwner;

    /**
     * @brief   list of cardtypes to choose from
     **/
    protected $cardtypes;
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
        $this->defaults['labelNumber']          = "Creditcard Number";
        $this->defaults['labelCheck']           = "CVV/CVC";
        $this->defaults['labelExpirationDate']  = "Expiration Date MM/YY";
        $this->defaults['labelOwner']           = "Card Owner";
        $this->defaults['cardtypes']            = [
            "visa",
            "americanexpress",
            "mastercard",
        ];
    }
    // }}}

    // {{{ addChildElements()
    /**
     * @brief   adds creditcard-inputs to fieldset
     *
     * @todo    check regular expressions based on (?): http://www.regular-expressions.info/creditcard.html
     *
     * @return void
     **/
    public function addChildElements(): void
    {
        parent::addChildElements();

        $cardnames = [
            'visa' => "Visa",
            'americanexpress' => "American Express",
            'mastercard' => "MasterCard",
        ];
        $options = [];
        foreach ($this->cardtypes as $card) {
            if (isset($cardnames[$card])) {
                $options[$card] = $cardnames[$card];
            }
        }

        $this->addSingle($this->name . "_card_type", [
            'label' => "",
            'list' => $options,
            'skin' => 'select',
        ]);
        $this->addText($this->name . "_card_number", [
            'label' => $this->labelNumber,
            'required' => $this->required,
            'validator' => "/^(?:\d[ -]*?){13,16}$/",
        ]);
        $this->addText($this->name . "_card_numbercheck", [
            'label' => $this->labelCheck,
            'required' => $this->required,
            'validator' => "/^\d{3,4}$/",
        ]);
        $this->addText($this->name . "_card_expirydate", [
            'label' => $this->labelExpirationDate,
            'required' => $this->required,
            'validator' => "/^\d{2}\/\d{2}$/",
        ]);
        $this->addText($this->name . "_card_owner", [
            'label' => $this->labelOwner,
            'required' => $this->required,
        ]);
    }
    // }}}

    // {{{ validate()
    /**
     * @brief   Validate the creditcard data
     *
     * @todo    validate based on (?): http://www-sst.informatik.tu-cottbus.de/~db/doc/Java/GalileoComputing-JavaInsel/java-04.htm#t321
     * @todo    validate specific to cardtype
     *
     * @return bool validation result
     **/
    public function validate(): bool
    {
        return parent::validate();
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
