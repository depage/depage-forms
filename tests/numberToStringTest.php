<?php

use depage\htmlform\elements\number;

class numberToStringTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $parameters = array();
        $this->form     = new nameTestForm;
        $this->number   = new number('numberName', $parameters, $this->form);
    }

    public function testSimple() {
        $expected = '<p id="formName-numberName" class="input-number" data-errorMessage="Please enter a valid number!">' .
            '<label>' .
                '<span class="label">numberName</span>' .
                '<input name="numberName" type="number" value="0">' .
            '</label>' .
        '</p>' . "\n";

        $this->assertEquals($expected, $this->number->__toString());
    }

    public function testValue() {
        $expected = '<p id="formName-numberName" class="input-number" data-errorMessage="Please enter a valid number!">' .
            '<label>' .
                '<span class="label">numberName</span>' .
                '<input name="numberName" type="number" value="7331">' .
            '</label>' .
        '</p>' . "\n";

        $this->number->setValue(7331);
        $this->assertEquals($expected, $this->number->__toString());
    }

    public function testRequired() {
        $expected = '<p id="formName-numberName" class="input-number required" data-errorMessage="Please enter a valid number!">' .
            '<label>' .
                '<span class="label">numberName <em>*</em></span>' .
                '<input name="numberName" type="number" required value="0">' .
            '</label>' .
        '</p>' . "\n";

        $this->number->setRequired();
        $this->assertEquals($expected, $this->number->__toString());
    }

    public function testHtmlEscaping() {
        $expected = '<p id="formName-numberName" class="input-number required" title="ti&quot;&gt;tle" data-errorMessage="er&quot;&gt;rorMessage">' .
            '<label>' .
                '<span class="label">la&quot;&gt;bel <em>ma&quot;&gt;rker</em></span>' .
                '<input name="numberName" type="number" required value="0">' .
            '</label>' .
        '</p>' . "\n";

        $parameters = array(
            'label'         => 'la">bel',
            'marker'        => 'ma">rker',
            'errorMessage'  => 'er">rorMessage',
            'title'         => 'ti">tle',
            'required'      => true,
        );
        $number = new number('numberName', $parameters, $this->form);
        $this->assertEquals($expected, $number->__toString());
    }
}
