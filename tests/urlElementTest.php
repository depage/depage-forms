<?php

use depage\htmlform\elements\url;

class urlElementTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $parameters = array();
        $this->form = new nameTestForm;
        $this->url  = new url('urlName', $parameters, $this->form);
    }

    public function testUrlSetValue() {
        $this->assertEquals($this->url->getName(), 'urlName');

        $this->url->setValue('valueString');
        $this->assertEquals($this->url->getValue(), 'valueString');
    }

    public function testUrlNotRequiredEmpty() {
        $this->url->setValue('');
        $this->assertEquals($this->url->validate(), true);
    }

    public function testUrlValidNotRequiredNotEmpty() {
        $this->url->setValue('http://www.depage.com');
        $this->assertEquals($this->url->validate(), true);
    }

    public function testUrlInvalidNotRequired() {
        $this->url->setValue('valueString');
        $this->assertEquals($this->url->validate(), false);
    }

    public function testUrlRequiredEmpty() {
        $this->url->setRequired();
        $this->url->setValue('');
        $this->assertEquals($this->url->validate(), false);
    }
}
