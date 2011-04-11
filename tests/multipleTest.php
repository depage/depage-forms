<?php

use depage\htmlform\elements\multiple;

/**
 * General tests for the multiple input element.
 **/
class multipleTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->form     = new nameTestForm;
        $this->multiple = new multiple('multipleName', array(), $this->form);
    }

    /**
     * Constructor test, getName()
     **/
    public function testConstruct() {
        $this->assertEquals('multipleName', $this->multiple->getName());
    }

    /**
     * Tests setValue method with various values. (typecasting)
     **/
    public function testMultipleSetValue() {
        $this->multiple->setValue(array('1'=>'1'));
        $this->assertEquals(array('1'=>'1'), $this->multiple->getValue());

        $this->multiple->setValue('');
        $this->assertEquals(array(), $this->multiple->getValue());
    }

    /**
     * Not required, empty -> valid
     **/
    public function testMultipleNotRequiredEmpty() {
        $this->multiple->setValue(array());
        $this->assertTrue($this->multiple->validate());
    }

    /**
     * Not required, valid -> valid
     **/
    public function testMultipleValidNotRequiredNotEmpty() {
        $this->multiple->setValue(array('1'=>'1'));
        $this->assertTrue($this->multiple->validate());
    }

    /**
     * Required, empty -> invalid
     **/
    public function testMultipleRequiredEmpty() {
        $this->multiple->setRequired();
        $this->multiple->setValue(array());
        $this->assertFalse($this->multiple->validate());
    }
}
