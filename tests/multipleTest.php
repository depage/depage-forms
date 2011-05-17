<?php

use depage\htmlform\elements\multiple;

/**
 * General tests for the multiple input element.
 **/
class multipleTest extends PHPUnit_Framework_TestCase {
    // {{{ setUp()
    public function setUp() {
        $this->form     = new nameTestForm;
        $this->multiple = new multiple('multipleName', array(), $this->form);
    }
    // }}}

    // {{{ testConstruct()
    /**
     * Constructor test, getName()
     **/
    public function testConstruct() {
        $this->assertEquals('multipleName', $this->multiple->getName());
    }
    // }}}

    // {{{ testMultipleSetValue()
    /**
     * Tests setValue method with various values. (typecasting)
     **/
    public function testMultipleSetValue() {
        $this->multiple->setValue(array('1'=>'1'));
        $this->assertEquals(array('1'=>'1'), $this->multiple->getValue());

        $this->multiple->setValue('');
        $this->assertEquals(array(), $this->multiple->getValue());
    }
    // }}}

    // {{{ testMultipleNotRequiredEmpty()
    /**
     * Not required, empty -> valid
     **/
    public function testMultipleNotRequiredEmpty() {
        $this->multiple->setValue(array());
        $this->assertTrue($this->multiple->validate());
    }
    // }}}

    // {{{ testMultipleValidNotRequiredNotEmpty()
    /**
     * Not required, valid -> valid
     **/
    public function testMultipleValidNotRequiredNotEmpty() {
        $this->multiple->setValue(array('1'=>'1'));
        $this->assertTrue($this->multiple->validate());
    }
    // }}}

    // {{{ testMultipleRequiredEmpty()
    /**
     * Required, empty -> invalid
     **/
    public function testMultipleRequiredEmpty() {
        $this->multiple->setRequired();
        $this->multiple->setValue(array());
        $this->assertFalse($this->multiple->validate());
    }
    // }}}
}
