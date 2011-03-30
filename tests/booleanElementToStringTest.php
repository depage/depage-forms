<?php

require_once('../elements/boolean.php');

use depage\htmlform\elements\boolean;

class booleanElementToStringTest extends PHPUnit_Framework_TestCase {
    public function testSimple() {
        $expected = '<p id="formName-elementName" class="input-boolean" data-errorMessage="Please check this box if you want to proceed!">' .
            '<label>' .
                '<input type="checkbox" name="elementName" value="true">' .
                '<span class="label">elementName</span>' .
            '</label>' .
        '</p>' . "\n";

        $boolean = new boolean('elementName', $ref = array(), 'formName');
        $this->assertEquals($expected, $boolean->__toString());
    }

    public function testChecked() {
        $expected = '<p id="formName-elementName" class="input-boolean" data-errorMessage="Please check this box if you want to proceed!">' .
            '<label>' .
                '<input type="checkbox" name="elementName" value="true" checked="yes">' .
                '<span class="label">elementName</span>' .
            '</label>' .
        '</p>' . "\n";

        $boolean = new boolean('elementName', $ref = array(), 'formName');
        $boolean->setValue(true);
        $this->assertEquals($expected, $boolean->__toString());
    }

    public function testRequired() {
        $expected = '<p id="formName-elementName" class="input-boolean required" data-errorMessage="Please check this box if you want to proceed!">' .
            '<label>' .
                '<input type="checkbox" name="elementName" required value="true">' .
                '<span class="label">elementName <em>*</em></span>' .
            '</label>' .
        '</p>' . "\n";

        $boolean = new boolean('elementName', $ref = array(), 'formName');
        $boolean->setRequired();
        $this->assertEquals($expected, $boolean->__toString());
    }

    public function testHtmlEscaping() {
        $expected = '<p id="formName-elementName" class="input-boolean" data-errorMessage="er&quot;&gt;rorMessage">' .
            '<label title="ti&quot;&gt;tle">' .
                '<input type="checkbox" name="elementName" value="true">' .
                '<span class="label">la&quot;&gt;bel</span>' .
            '</label>' .
        '</p>' . "\n";

        $parameters = array(
            'label'         => 'la">bel',
            'marker'        => 'ma">rker',
            'errorMessage'  => 'er">rorMessage',
            'title'         => 'ti">tle',
        );
        $boolean = new boolean('elementName', $parameters, 'formName');
        $this->assertEquals($expected, $boolean->__toString());
    }
}
