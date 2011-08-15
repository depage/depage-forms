<?php

use depage\htmlform\htmlform;
use depage\htmlform\elements\fieldset;

/**
 * Tests for fieldset container element rendering.
 **/
class fieldsetToStringTest extends PHPUnit_Framework_TestCase {
    // {{{ setUp()
    public function setUp() {
        $this->form = new nameTestForm('formName');
        $this->fieldset = new fieldset('fieldsetName', array(), $this->form);
    }
    // }}}

    // {{{ testSimple()
    /**
     * Element with default setup
     **/
    public function testSimple() {
        $expected = '<fieldset id="formName-fieldsetName" name="fieldsetName">' .
                        '<legend>fieldsetName</legend>' .
                    '</fieldset>' . "\n";
        $this->assertEquals($expected, $this->fieldset->__toString());
    }
    // }}}

    // {{{ testAddFieldset()
    /**
     * With fieldset subelement.
     **/
    public function testAddFieldset() {
        $expected = '<fieldset id="formName-fieldsetName" name="fieldsetName">' .
            '<legend>fieldsetName</legend>' .
            '<fieldset id="formName-secondFieldsetName" name="secondFieldsetName">' .
                '<legend>secondFieldsetName</legend>' .
            '</fieldset>' . "\n" .
        '</fieldset>' . "\n";
        $this->fieldset->addFieldset('secondFieldsetName');

        $this->assertEquals($expected, $this->fieldset->__toString());
    }
    // }}}

    // {{{ testAddText()
    /**
     * With text subelement
     **/
    public function testAddText() {
        $expected = '<fieldset id="formName-fieldsetName" name="fieldsetName">' .
            '<legend>fieldsetName</legend>' .
            '<p id="formName-textName" class="input-text" data-errorMessage="Please enter valid data">' .
                '<label>' .
                    '<span class="label">textName</span>' .
                    '<input name="textName" type="text" value="">' .
                '</label>' .
            '</p>' . "\n" .
        '</fieldset>' . "\n";

        $this->fieldset->addText('textName');
        $this->assertEquals($expected, $this->fieldset->__toString());
    }
    // }}}
}
