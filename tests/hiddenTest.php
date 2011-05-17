<?php

use depage\htmlform\elements\hidden;

/**
 * General tests for the hidden input element.
 **/
class hiddenTest extends PHPUnit_Framework_TestCase {
    // {{{ setUp()
    public function setUp() {
        $this->form     = new nameTestForm;
        $this->hidden   = new hidden('nameString', array(), $this->form);
    }
    // }}}

    // {{{ testHiddenSetValue()
    /**
     * Tests setValue method.
     **/
    public function testHiddenSetValue() {
        $this->hidden->setValue('valueString');
        $this->assertEquals('valueString', $this->hidden->getValue());
    }
    // }}}
}
