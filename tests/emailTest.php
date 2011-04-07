<?php

use depage\htmlform\elements\email;

class emailTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->form     = new nameTestForm;
        $this->email    = new email('emailName', array(), $this->form);
    }

    public function testEmailSetValue() {
        $this->assertEquals('emailName', $this->email->getName());

        $this->email->setValue('valueString');
        $this->assertEquals('valueString', $this->email->getValue());
    }

    public function testEmailNotRequiredEmpty() {
        $this->email->setValue('');
        $this->assertTrue($this->email->validate());
    }

    public function testEmailValidNotRequiredNotEmpty() {
        $this->email->setValue('mail@depage.com');
        $this->assertTrue($this->email->validate());
    }

    public function testEmailInvalidNotRequired() {
        $this->email->setValue('valueString');
        $this->assertFalse($this->email->validate());
    }

    public function testEmailRequiredEmpty() {
        $this->email->setRequired();
        $this->email->setValue('');
        $this->assertFalse($this->email->validate());
    }
}
