<?php

use PHPUnit\Framework\TestCase;
use Depage\HtmlForm\Elements\Email;

/**
 * General tests for the email input element.
 **/
class emailTest extends TestCase
{
    // {{{ setUp()
    public function setUp():void
    {
        $this->form     = new nameTestForm;
        $this->email    = new Email('emailName', array(), $this->form);
    }
    // }}}

    // {{{ testEmailSetValue()
    /**
     * Tests setValue method with various values.
     **/
    public function testEmailSetValue()
    {
        // usual value
        $this->email->setValue('valueString');
        $this->assertEquals('valueString', $this->email->getValue());

        // tests type casting
        $this->email->setValue(42);
        $this->assertIsString($this->email->getValue());
        $this->assertEquals('42', $this->email->getValue());
    }
    // }}}

    // {{{ testEmailNotRequiredEmpty()
    /**
     * Should be valid when empty but not required.
     **/
    public function testEmailNotRequiredEmpty()
    {
        $this->email->setValue('');
        $this->assertTrue($this->email->validate());
    }
    // }}}

    // {{{ testEmailValidNotRequiredNotEmpty()
    /**
     * Should be valid with valid non-empty value when not required.
     **/
    public function testEmailValidNotRequiredNotEmpty()
    {
        $this->email->setValue('mail@depage.com');
        $this->assertTrue($this->email->validate());
    }
    // }}}

    // {{{ testEmailInvalidNotRequired()
    /**
     * Invalid value, not required => invalid element.
     **/
    public function testEmailInvalidNotRequired()
    {
        $this->email->setValue('valueString');
        $this->assertFalse($this->email->validate());
    }
    // }}}

    // {{{ testEmailRequiredEmpty()
    /**
     * Empty value, required => invalid element.
     **/
    public function testEmailRequiredEmpty()
    {
        $this->email->setRequired();
        $this->email->setValue('');
        $this->assertFalse($this->email->validate());
    }
    // }}}
}
