<?php

use depage\htmlform\htmlform;
use depage\htmlform\elements\fieldset;

/**
 * General tests for the fieldset element.
 **/
class fieldsetTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->form = new nameTestForm('formName');
        $this->fieldset = new fieldset('fieldsetName', array(), $this->form);
    }

    /**
     * Tests __call method and adding container subelements
     **/
    public function testAddFieldset() {
        $fieldset2  = $this->fieldset->addFieldset('secondFieldsetName');
        // include fieldsets true (getElements(true))
        $elements   = $this->fieldset->getElements(true);

        $this->assertEquals($elements[0], $fieldset2);
    }

    /**
     * Tests __call method and adding input subelements
     **/
    public function testAddText() {
        $text       = $this->fieldset->addText('textName');
        $elements   = $this->fieldset->getElements();

        $this->assertEquals($text, $elements[0]);
    }
}
