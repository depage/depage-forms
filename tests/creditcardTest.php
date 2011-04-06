<?php

use depage\htmlform\htmlform;
use depage\htmlform\elements\creditcard;

class creditcardTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->form = new nameTestForm;
        $this->creditcard = new creditcard('creditcardName', array(), $this->form);
    }

    public function testGetName() {
        $this->assertEquals('creditcardName', $this->creditcard->getName());
    }
}
