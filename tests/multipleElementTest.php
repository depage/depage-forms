<?php

require_once('../elements/multiple.php');

use depage\htmlform\elements\multiple;

class multipleElementTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->multiple = new multiple('nameString', $ref = array(), 'formName');
    }

    public function testMultipleSetValue() {
        $this->assertEquals($this->multiple->getName(), 'nameString');

        $this->multiple->setValue(array('1'=>'1'));
        $this->assertEquals($this->multiple->getValue(), array('1'=>'1'));
    }

    public function testMultipleNotRequiredEmpty() {
        $this->multiple->setValue(array());
        $this->assertEquals($this->multiple->validate(), true);
    }

    public function testMultipleValidNotRequiredNotEmpty() {
        $this->multiple->setValue(array('1'=>'1'));
        $this->assertEquals($this->multiple->validate(), true);
    }

    public function testMultipleRequiredEmpty() {
        $this->multiple->setRequired();
        $this->multiple->setValue(array());
        $this->assertEquals($this->multiple->validate(), false);
    }
}
