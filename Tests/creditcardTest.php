<?php

use PHPUnit\Framework\TestCase;
use Depage\HtmlForm\HtmlForm;
use Depage\HtmlForm\Elements\Creditcard;

/**
 * General tests for the creditcard element.
 **/
class creditcardTest extends TestCase
{
    protected $form;
    protected $creditcard;

    // {{{ setUp()
    public function setUp(): void
    {
        $this->form = new nameTestForm();
        $this->creditcard = new Creditcard('creditcardName', [], $this->form);
        $this->creditcard->addChildElements();
    }
    // }}}

    // {{{ testGetName()
    /**
     * Constructor test, getName()
     **/
    public function testGetName()
    {
        $this->assertEquals('creditcardName', $this->creditcard->getName());
    }
    // }}}

    // {{{ testValidateInvalid()
    /**
     * Creditcard is invalid in default status
     **/
    public function testValidateInvalid()
    {
        $this->assertFalse($this->creditcard->validate());
    }
    // }}}

    // {{{ testValidateValid()
    /**
     * Valid subelements -> valid creditcard
     **/
    public function testValidateValid()
    {
        $form       = new HtmlForm('formName');
        $creditcard = $form->addCreditcard('creditcardName');

        $form->getElement('creditcardName_card_type')->setValue('visa');
        $form->getElement('creditcardName_card_number')->setValue('12345678901234');
        $form->getElement('creditcardName_card_numbercheck')->setValue('123');
        $form->getElement('creditcardName_card_expirydate')->setValue('01/01');
        $form->getElement('creditcardName_card_owner')->setValue('me');

        $this->assertTrue($creditcard->validate());
    }
    // }}}
}
