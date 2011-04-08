<?php

use depage\htmlform\elements\boolean;

/**
 * General tests for the boolean input element.
 **/
class booleanTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->form     = new nameTestForm();
        $this->boolean  = new boolean('booleanName', array(), $this->form);
    }

    /**
     * Constructor test.
     **/
    public function testConstruct() {
        $this->assertEquals('booleanName', $this->boolean->getName());
    }

    /**
     * Tests setValue method with various values.
     **/
    public function testBooleanSetValue() {
        $this->boolean->setValue(true);
        $this->assertTrue($this->boolean->getValue());

        // (string) 'true' counts as (bool) true
        $this->boolean->setValue('true');
        $this->assertTrue($this->boolean->getValue());

        // any other values count as false
        $this->boolean->setValue('foo');
        $this->assertFalse($this->boolean->getValue());

        $this->boolean->setValue(array());
        $this->assertFalse($this->boolean->getValue());

        $this->boolean->setValue(1);
        $this->assertFalse($this->boolean->getValue());
    }

    /**
     * Should be valid when false but not required.
     **/
    public function testNotRequiredFalse() {
        $this->boolean->setValue(false);
        $this->assertTrue($this->boolean->validate());
    }

    /**
     * Element is invalid if false and required.
     **/
    public function testRequiredFalse() {
        $this->boolean->setRequired();
        $this->boolean->setValue(false);
        $this->assertFalse($this->boolean->validate());
    }

    /**
     * Element is valid if true and not required.
     **/
    public function testNotRequiredTrue() {
        $this->boolean->setValue(true);
        $this->assertTrue($this->boolean->validate());
    }

    /**
     * Element is valid if true and required.
     **/
    public function testRequiredTrue() {
        $this->boolean->setValue(true);
        $this->boolean->setRequired();
        $this->assertTrue($this->boolean->validate());
    }
}
