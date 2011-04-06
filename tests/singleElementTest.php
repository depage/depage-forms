<?php

use depage\htmlform\elements\single;

class singleElementTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $parameters     = array();
        $this->form     = new nameTestForm;
        $this->single   = new single('singleName', $parameters, $this->form);
    }

    public function testSingleSetValue() {
        $this->assertEquals($this->single->getName(), 'singleName');

        $this->single->setValue('valueString');
        $this->assertEquals($this->single->getValue(), 'valueString');
    }

    public function testSingleNotRequiredEmpty() {
        $this->single->setValue('');
        $this->assertEquals($this->single->validate(), true);
    }

    public function testSingleValidNotRequiredNotEmpty() {
        $this->single->setValue('valueString');
        $this->assertEquals($this->single->validate(), true);
    }

    public function testSingleRequiredEmpty() {
        $this->single->setRequired();
        $this->single->setValue('');
        $this->assertEquals($this->single->validate(), false);
    }
}
