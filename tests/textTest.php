<?php

use depage\htmlform\elements\text;

/**
 * General tests for the text input element.
 **/
class textTest extends PHPUnit_Framework_TestCase {
    // {{{ setUp()
    public function setUp() {
        $this->form = new nameTestForm;
        $this->text = new text('textName', array(), $this->form);
    }
    // }}}

    // {{{ testGetName()
    /**
     * Constructor test, getName()
     **/
    public function testGetName() {
        $this->assertEquals('textName', $this->text->getName());
    }
    // }}}

    // {{{ testTextSetValue()
    /**
     * Tests setValue method with various values. (typecasting)
     **/
    public function testTextSetValue() {
        $this->text->setValue('valueString');
        $this->assertEquals('valueString', $this->text->getValue());

        $this->text->setValue(42);
        $this->assertInternalType('string', $this->text->getValue());
        $this->assertEquals('42', $this->text->getValue());
    }
    // }}}

    // {{{ testTextNotRequiredEmpty()
    /**
     * Not required, empty -> valid
     **/
    public function testTextNotRequiredEmpty() {
        $this->text->setValue('');
        $this->assertTrue($this->text->validate());
    }
    // }}}

    // {{{ testTextValidNotRequiredNotEmpty()
    /**
     * Not required, not empty -> valid
     **/
    public function testTextValidNotRequiredNotEmpty() {
        $this->text->setValue('valueString');
        $this->assertTrue($this->text->validate());
    }
    // }}}

    // {{{ testTextRequiredEmpty()
    /**
     * Required, empty -> invalid
     **/
    public function testTextRequiredEmpty() {
        $this->text->setRequired();
        $this->text->setValue('');
        $this->assertFalse($this->text->validate());
    }
    // }}}
}
