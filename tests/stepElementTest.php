<?php

use depage\htmlform\htmlform;
use depage\htmlform\elements\step;

class stepElementTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->step = new step('stepName');

        $this->form = new htmlform('formName');
        $this->step->setParentForm($this->form);
    }

    public function testToString() {
        $expected = '';
        $this->assertEquals($expected, $this->step->__toString());
    }

    public function testAddElementFieldset() {
        $expected = '<fieldset id="formName-fieldsetName" name="fieldsetName">' .
            '<legend>fieldsetName</legend>' .
        '</fieldset>' . "\n";
        $this->step->addElement('depage\\htmlform\\elements\\fieldset', 'fieldsetName');

        $this->assertEquals($expected, $this->step->__toString());
    }

    public function testAddElementText() {
        $expected = '<p id="formName-textName" class="input-text" data-errorMessage="Please enter valid data!">' .
                '<label>' .
                    '<span class="label">textName</span>' .
                    '<input name="textName" type="text" value="">' .
                '</label>' .
            '</p>' . "\n";

        $this->step->addElement('depage\\htmlform\\elements\\text', 'textName');
        $this->assertEquals($expected, $this->step->__toString());
    }
}
