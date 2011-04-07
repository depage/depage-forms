<?php

use depage\htmlform\elements\text;

class textTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->form = new nameTestForm;
        $this->text = new text('textName', array(), $this->form);
    }

    public function testGetName() {
        $this->assertEquals('textName', $this->text->getName());
    }

    public function testTextSetValue() {
        $this->text->setValue('valueString');
        $this->assertEquals('valueString', $this->text->getValue());
    }

    public function testTextNotRequiredEmpty() {
        $this->text->setValue('');
        $this->assertTrue($this->text->validate());
    }

    public function testTextValidNotRequiredNotEmpty() {
        $this->text->setValue('valueString');
        $this->assertTrue($this->text->validate());
    }

    public function testTextRequiredEmpty() {
        $this->text->setRequired();
        $this->text->setValue('');
        $this->assertFalse($this->text->validate());
    }
}
