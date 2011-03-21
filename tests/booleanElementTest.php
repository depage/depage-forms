<?php

require_once('../abstracts/input.php');
require_once('../elements/boolean.php');

use depage\htmlform\elements\boolean;

class booleanElementTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->boolean = new boolean('nameString', $ref = array(), 'formName');
    }

    public function testBooleanSetValue() {
        $this->assertEquals($this->boolean->getName(), 'nameString');

        $this->boolean->setValue(true);
        $this->assertEquals($this->boolean->getValue(), true);
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
