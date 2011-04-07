<?php

use depage\htmlform\elements\boolean;

class booleanTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->form     = new nameTestForm();
        $this->boolean  = new boolean('booleanName', array(), $this->form);
    }

    public function testBooleanInstantiate() {
        $this->assertEquals('booleanName', $this->boolean->getName());
    }

    public function testBooleanSetValue() {
        $this->boolean->setValue(true);
        $this->assertTrue($this->boolean->getValue());

        $this->boolean->setValue('true');
        $this->assertTrue($this->boolean->getValue());

        $this->boolean->setValue('foo');
        $this->assertFalse($this->boolean->getValue());

        $this->boolean->setValue(array());
        $this->assertFalse($this->boolean->getValue());

        $this->boolean->setValue(1);
        $this->assertFalse($this->boolean->getValue());
    }

    public function testBooleanNotRequiredFalse() {
        $this->boolean->setValue(false);
        $this->assertTrue($this->boolean->validate());
    }

    public function testBooleanRequiredFalse() {
        $this->boolean->setRequired();
        $this->boolean->setValue(false);
        $this->assertFalse($this->boolean->validate());
    }

    public function testBooleanNotRequiredTrue() {
        $this->boolean->setValue(true);
        $this->assertTrue($this->boolean->validate());
    }

    public function testBooleanRequiredTrue() {
        $this->boolean->setValue(true);
        $this->boolean->setRequired();
        $this->assertTrue($this->boolean->validate());
    }
}
