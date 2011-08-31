<?php

use depage\htmlform\elements\single;

/**
 * General tests for the multiple input element.
 **/
class singleTest extends PHPUnit_Framework_TestCase {
    // {{{
    public function setUp() {
        $this->form     = new nameTestForm;
        $this->single   = new single('singleName', array(), $this->form);
    }
    // }}}

    // {{{ testConstruct()
    /**
     * Constructor test, getName()
     **/
    public function testConstruct() {
        $this->assertEquals('singleName', $this->single->getName());
    }
    // }}}

    // {{{ testSingleSetValue()
    /**
     * Tests setValue method with various values. (typecasting)
     **/
    public function testSingleSetValue() {
        $this->single->setValue('valueString');
        $this->assertEquals('valueString', $this->single->getValue());

        $this->single->setValue(42);
        $this->assertInternalType('string', $this->single->getValue());
        $this->assertEquals('42', $this->single->getValue());
    }
    // }}}

    // {{{ testSingleNotRequiredEmpty()
    /**
     * Not Required, empty -> valid
     **/
    public function testSingleNotRequiredEmpty() {
        $this->single->setValue('');
        $this->assertTrue($this->single->validate());
    }
    // }}}

    // {{{ testSingleValidNotRequiredNotEmpty()
    /**
     * Not empty, not required -> valid
     **/
    public function testSingleValidNotRequiredNotEmpty() {
        $this->single->setValue('valueString');
        $this->assertTrue($this->single->validate());
    }
    // }}}

    // {{{ testSingleRequiredEmpty()
    /**
     * Required, empty -> invalid
     **/
    public function testSingleRequiredEmpty() {
        $this->single->setRequired();
        $this->single->setValue('');
        $this->assertFalse($this->single->validate());
    }
    // }}}
}
