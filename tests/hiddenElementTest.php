<?php

require_once('../elements/hidden.php');

use depage\htmlform\elements\hidden;

class hiddenElementTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $parameters = array();
        $this->hidden = new hidden('nameString', $parameters, 'formName');
    }

    public function testHiddenSetValue() {
        $this->assertEquals($this->hidden->getName(), 'nameString');

        $this->hidden->setValue('valueString');
        $this->assertEquals($this->hidden->getValue(), 'valueString');
    }
}
