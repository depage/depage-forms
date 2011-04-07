<?php

use depage\htmlform\elements\multiple;

class multipleTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->form     = new nameTestForm;
        $this->multiple = new multiple('nameString', array(), $this->form);
    }

    public function testMultipleSetValue() {
        $this->assertEquals('nameString', $this->multiple->getName());

        $this->multiple->setValue(array('1'=>'1'));
        $this->assertEquals(array('1'=>'1'), $this->multiple->getValue());

        $this->multiple->setValue('');
        $this->assertEquals(array(), $this->multiple->getValue());
    }

    public function testMultipleNotRequiredEmpty() {
        $this->multiple->setValue(array());
        $this->assertTrue($this->multiple->validate());
    }

    public function testMultipleValidNotRequiredNotEmpty() {
        $this->multiple->setValue(array('1'=>'1'));
        $this->assertTrue($this->multiple->validate());
    }

    public function testMultipleRequiredEmpty() {
        $this->multiple->setRequired();
        $this->multiple->setValue(array());
        $this->assertFalse($this->multiple->validate());
    }
}
