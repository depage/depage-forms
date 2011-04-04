<?php

require_once('../htmlform.php');

use depage\htmlform\htmlform;
use depage\htmlform\elements\fieldset;

class fieldsetElementTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->fieldset = new fieldset('fieldsetName');

        $this->form = new htmlform('formName');
        $this->fieldset->setParentForm($this->form);
    }

    public function testToString() {
        $expected = '<fieldset id="formName-fieldsetName" name="fieldsetName">' .
                        '<legend>fieldsetName</legend>' .
                    '</fieldset>' . "\n";
        $this->assertEquals($expected, $this->fieldset->__toString());
    }

    public function testAddElementFieldset() {
        $expected = '<fieldset id="formName-fieldsetName" name="fieldsetName">' .
            '<legend>fieldsetName</legend>' .
            '<fieldset id="formName-secondFieldsetName" name="secondFieldsetName">' .
                '<legend>secondFieldsetName</legend>' .
            '</fieldset>' . "\n" .
        '</fieldset>' . "\n";
        $this->fieldset->addElement('depage\\htmlform\\elements\\fieldset', 'secondFieldsetName');

        $this->assertEquals($expected, $this->fieldset->__toString());
    }

    public function testAddElementText() {
        $expected = '<fieldset id="formName-fieldsetName" name="fieldsetName">' .
            '<legend>fieldsetName</legend>' .
            '<p id="formName-textName" class="input-text" data-errorMessage="Please enter valid data!">' .
                '<label>' .
                    '<span class="label">textName</span>' .
                    '<input name="textName" type="text" value="">' .
                '</label>' .
            '</p>' . "\n" .
        '</fieldset>' . "\n";

        $this->fieldset->addElement('depage\\htmlform\\elements\\text', 'textName');
        $this->assertEquals($expected, $this->fieldset->__toString());
    }
}
