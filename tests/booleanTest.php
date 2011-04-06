<?php

use depage\htmlform\elements\boolean;

class booleanTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->form     = new nameTestForm();
        $this->boolean  = new boolean('nameString', array(), $this->form);
    }

    public function testBooleanInstantiate() {
        $this->assertEquals($this->boolean->getName(), 'nameString');
    }

    public function testBooleanSetValue() {
        $this->boolean->setValue(true);
        $this->assertEquals($this->boolean->getValue(), true);

        $this->boolean->setValue('true');
        $this->assertEquals($this->boolean->getValue(), true);

        $this->boolean->setValue('foo');
        $this->assertEquals($this->boolean->getValue(), false);

        $this->boolean->setValue(array());
        $this->assertEquals($this->boolean->getValue(), false);

        $this->boolean->setValue(1);
        $this->assertEquals($this->boolean->getValue(), false);
    }

    public function testBooleanNotRequiredFalse() {
        $this->boolean->setValue(false);
        $this->assertEquals($this->boolean->validate(), true);
    }

    public function testBooleanRequiredFalse() {
        $this->boolean->setRequired();
        $this->boolean->setValue(false);
        $this->assertEquals($this->boolean->validate(), false);
    }

    public function testBooleanNotRequiredTrue() {
        $this->boolean->setValue(true);
        $this->assertEquals($this->boolean->validate(), true);
    }

    public function testBooleanRequiredTrue() {
        $this->boolean->setValue(true);
        $this->boolean->setRequired();
        $this->assertEquals($this->boolean->validate(), true);
    }
}
