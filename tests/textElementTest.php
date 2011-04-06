<?php

use depage\htmlform\elements\text;

class textElementTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $parameters = array();
        $this->form = new nameTestForm;
        $this->text = new text('nameString', $parameters, $this->form);
    }

    public function testGetName() {
        $this->assertEquals($this->text->getName(), 'nameString');
    }

    public function testTextSetValue() {
        $this->text->setValue('valueString');
        $this->assertEquals($this->text->getValue(), 'valueString');
    }

    public function testTextNotRequiredEmpty() {
        $this->text->setValue('');
        $this->assertEquals($this->text->validate(), true);
    }

    public function testTextValidNotRequiredNotEmpty() {
        $this->text->setValue('valueString');
        $this->assertEquals($this->text->validate(), true);
    }

    public function testTextRequiredEmpty() {
        $this->text->setRequired();
        $this->text->setValue('');
        $this->assertEquals($this->text->validate(), false);
    }
}
