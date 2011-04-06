<?php

use depage\htmlform\elements\hidden;

class hiddenTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $parameters = array();
        $this->form     = new nameTestForm;
        $this->hidden   = new hidden('nameString', $parameters, $this->form);
    }

    public function testHiddenSetValue() {
        $this->assertEquals($this->hidden->getName(), 'nameString');

        $this->hidden->setValue('valueString');
        $this->assertEquals($this->hidden->getValue(), 'valueString');
    }
}
