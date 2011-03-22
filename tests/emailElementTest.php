<?php

require_once('../elements/email.php');

use depage\htmlform\elements\email;

class emailElementTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->email = new email('nameString', $ref = array(), 'formName');
    }

    public function testEmailSetValue() {
        $this->assertEquals($this->email->getName(), 'nameString');

        $this->email->setValue('valueString');
        $this->assertEquals($this->email->getValue(), 'valueString');
    }

    public function testEmailNotRequiredEmpty() {
        $this->email->setValue('');
        $this->assertEquals($this->email->validate(), true);
    }

    public function testEmailValidNotRequiredNotEmpty() {
        $this->email->setValue('mail@depage.com');
        $this->assertEquals($this->email->validate(), true);
    }

    public function testEmailInvalidNotRequired() {
        $this->email->setValue('valueString');
        $this->assertEquals($this->email->validate(), false);
    }

    public function testEmailRequiredEmpty() {
        $this->email->setRequired();
        $this->email->setValue('');
        $this->assertEquals($this->email->validate(), false);
    }
}
