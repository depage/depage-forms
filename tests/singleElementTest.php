<?php

require_once('../elements/single.php');

use depage\htmlform\elements\single;

class singleElementTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->single = new single('nameString', $ref = array(), 'formName');
    }

    public function testSingleSetValue() {
        $this->assertEquals($this->single->getName(), 'nameString');

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
