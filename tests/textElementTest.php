<?php

require_once('../elements/text.php');

use depage\htmlform\elements\text;

class textElementTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->text = new text('nameString', $ref = array(), 'formName');
    }

    public function testTextSetValue() {
        $this->assertEquals($this->text->getName(), 'nameString');

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
