<?php

use depage\htmlform\htmlform;
use depage\htmlform\elements\creditcard;

class creditcardTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->form = new nameTestForm;
        $this->creditcard = new creditcard('creditcardName', array(), $this->form);
        $this->creditcard->addChildElements();
    }

    public function testGetName() {
        $this->assertEquals('creditcardName', $this->creditcard->getName());
    }

    public function testValidateInvalid() {
        $this->assertFalse($this->creditcard->validate());
    }

    public function testValidateValid() {
        $form       = new htmlform('formName');
        $creditcard = $form->addCreditcard('creditcardName');

        $form->getElement('creditcardName_card_type')->setValue('visa');
        $form->getElement('creditcardName_card_number')->setValue('12345678901234');
        $form->getElement('creditcardName_card_numbercheck')->setValue('123');
        $form->getElement('creditcardName_card_expirydate')->setValue('01/01');
        $form->getElement('creditcardName_card_owner')->setValue('me');

        $this->assertTrue($creditcard->validate());
    }
}
