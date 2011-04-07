<?php

use depage\htmlform\elements\url;

class urlTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->form = new nameTestForm;
        $this->url  = new url('urlName', array(), $this->form);
    }

    public function testUrlSetValue() {
        $this->assertEquals('urlName', $this->url->getName());

        $this->url->setValue('valueString');
        $this->assertEquals('valueString', $this->url->getValue());
    }

    public function testUrlNotRequiredEmpty() {
        $this->url->setValue('');
        $this->assertTrue($this->url->validate());
    }

    public function testUrlValidNotRequiredNotEmpty() {
        $this->url->setValue('http://www.depage.com');
        $this->assertTrue($this->url->validate());
    }

    public function testUrlInvalidNotRequired() {
        $this->url->setValue('valueString');
        $this->assertFalse($this->url->validate());
    }

    public function testUrlRequiredEmpty() {
        $this->url->setRequired();
        $this->url->setValue('');
        $this->assertFalse($this->url->validate());
    }
}
