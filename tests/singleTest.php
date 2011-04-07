<?php

use depage\htmlform\elements\single;

class singleTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->form     = new nameTestForm;
        $this->single   = new single('singleName', array(), $this->form);
    }

    public function testSingleSetValue() {
        $this->assertEquals('singleName', $this->single->getName());

        $this->single->setValue('valueString');
        $this->assertEquals('valueString', $this->single->getValue());
    }

    public function testSingleNotRequiredEmpty() {
        $this->single->setValue('');
        $this->assertTrue($this->single->validate());
    }

    public function testSingleValidNotRequiredNotEmpty() {
        $this->single->setValue('valueString');
        $this->assertTrue($this->single->validate());
    }

    public function testSingleRequiredEmpty() {
        $this->single->setRequired();
        $this->single->setValue('');
        $this->assertFalse($this->single->validate());
    }
}
