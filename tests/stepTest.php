<?php

use depage\htmlform\htmlform;
use depage\htmlform\elements\step;

class stepTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->form = new nameTestForm;
        $this->step = new step('stepName', array(), $this->form);
    }

    public function testToString() {
        $expected = '';
        $this->assertEquals($expected, $this->step->__toString());
    }

    public function testAddFieldset() {
        $expected = '<fieldset id="formName-fieldsetName" name="fieldsetName">' .
            '<legend>fieldsetName</legend>' .
        '</fieldset>' . "\n";
        $this->step->addFieldset('fieldsetName');

        $this->assertEquals($expected, $this->step->__toString());
    }

    public function testAddText() {
        $expected = '<p id="formName-textName" class="input-text" data-errorMessage="Please enter valid data!">' .
                '<label>' .
                    '<span class="label">textName</span>' .
                    '<input name="textName" type="text" value="">' .
                '</label>' .
            '</p>' . "\n";

        $this->step->addText('textName');
        $this->assertEquals($expected, $this->step->__toString());
    }
}
