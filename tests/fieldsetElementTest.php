<?php

require_once('../htmlform.php');

use depage\htmlform\htmlform;
use depage\htmlform\elements\fieldset;

class fieldsetElementTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->form = new htmlform('formName');
        $this->fieldset = new fieldset('fieldsetName');

        $this->fieldset->setParentForm($this->form);
    }

    public function testToString() {
        $this->assertEquals($this->fieldset->__toString(), '<fieldset id="formName-fieldsetName" name="fieldsetName"><legend>fieldsetName</legend></fieldset>' . "\n");
    }

    public function testAddElement() {
        $addedFieldset = $this->fieldset->addElement('depage\\htmlform\\elements\\fieldset', 'secondFieldsetName');
        
        $this->assertEquals($this->fieldset->__toString(), '<fieldset id="formName-fieldsetName" name="fieldsetName"><legend>fieldsetName</legend>' . $addedFieldset . '</fieldset>' . "\n");
    }
}
