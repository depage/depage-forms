<?php

use depage\htmlform\elements\number;

class numberElementToStringTest extends PHPUnit_Framework_TestCase {
    public function testSimple() {
        $expected = '<p id="formName-elementName" class="input-number" data-errorMessage="Please enter a valid number!">' .
            '<label>' .
                '<span class="label">elementName</span>' .
                '<input name="elementName" type="number" value="0">' .
            '</label>' .
        '</p>' . "\n";

        $parameters = array();
        $number = new number('elementName', $parameters, 'formName');
        $this->assertEquals($expected, $number->__toString());
    }

    public function testValue() {
        $expected = '<p id="formName-elementName" class="input-number" data-errorMessage="Please enter a valid number!">' .
            '<label>' .
                '<span class="label">elementName</span>' .
                '<input name="elementName" type="number" value="7331">' .
            '</label>' .
        '</p>' . "\n";

        $parameters = array();
        $number = new number('elementName', $parameters, 'formName');
        $number->setValue(7331);
        $this->assertEquals($expected, $number->__toString());
    }

    public function testRequired() {
        $expected = '<p id="formName-elementName" class="input-number required" data-errorMessage="Please enter a valid number!">' .
            '<label>' .
                '<span class="label">elementName <em>*</em></span>' .
                '<input name="elementName" type="number" required value="0">' .
            '</label>' .
        '</p>' . "\n";

        $parameters = array();
        $number = new number('elementName', $parameters, 'formName');
        $number->setRequired();
        $this->assertEquals($expected, $number->__toString());
    }

    public function testHtmlEscaping() {
        $expected = '<p id="formName-elementName" class="input-number required" title="ti&quot;&gt;tle" data-errorMessage="er&quot;&gt;rorMessage">' .
            '<label>' .
                '<span class="label">la&quot;&gt;bel <em>ma&quot;&gt;rker</em></span>' .
                '<input name="elementName" type="number" required value="0">' .
            '</label>' .
        '</p>' . "\n";

        $parameters = array(
            'label'         => 'la">bel',
            'marker'        => 'ma">rker',
            'errorMessage'  => 'er">rorMessage',
            'title'         => 'ti">tle',
            'required'      => true,
        );
        $number = new number('elementName', $parameters, 'formName');
        $this->assertEquals($expected, $number->__toString());
    }
}
