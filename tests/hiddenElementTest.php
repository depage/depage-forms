<?php

require_once('../elements/hidden.php');

use depage\htmlform\elements\hidden;

class hiddenElementTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->hidden = new hidden('nameString', $ref = array(), 'formName');
    }

    public function testHiddenSetValue() {
        $this->assertEquals($this->hidden->getName(), 'nameString');

        $this->hidden->setValue('valueString');
        $this->assertEquals($this->hidden->getValue(), 'valueString');
    }
}
