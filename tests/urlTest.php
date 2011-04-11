<?php

use depage\htmlform\elements\url;

/**
 * General tests for the multiple input element.
 **/
class urlTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->form = new nameTestForm;
        $this->url  = new url('urlName', array(), $this->form);
    }

    /**
     * Constructor test, getName()
     **/
    public function testConstruct() {
        $this->assertEquals('urlName', $this->url->getName());
    }

    /**
     * Tests setValue method with various values. (typecasting)
     **/
    public function testSetValue() {
        $this->url->setValue('valueString');
        $this->assertEquals('valueString', $this->url->getValue());

        $this->url->setValue(42);
        $this->assertInternalType('string', $this->url->getValue());
        $this->assertEquals('42', $this->url->getValue());
    }

    /**
     * Not required, empty -> valid
     **/
    public function testNotRequiredEmpty() {
        $this->url->setValue('');
        $this->assertTrue($this->url->validate());
    }

    /**
     * Not required, not empty, valid value -> valid
     **/
    public function testValidNotRequiredNotEmpty() {
        $this->url->setValue('http://www.depage.com');
        $this->assertTrue($this->url->validate());
    }

    /**
     * Invalid value, not required -> invald
     **/
    public function testInvalidNotRequired() {
        $this->url->setValue('valueString');
        $this->assertFalse($this->url->validate());
    }

    /**
     * Required, empty -> invalid
     **/
    public function testRequiredEmpty() {
        $this->url->setRequired();
        $this->url->setValue('');
        $this->assertFalse($this->url->validate());
    }
}
