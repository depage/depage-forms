<?php

require_once('../abstracts/input.php');
require_once('../elements/datetimelocal.php');

use depage\htmlform\elements\datetimelocal;

class datetimelocalElementToStringTest extends PHPUnit_Framework_TestCase {
    public function testSimple() {
        $expected = '<p id="formName-elementName" class="input-datetimelocal" data-errorMessage="Please enter valid data!">' .
            '<label>' .
                '<span class="label">elementName</span>' .
                '<input name="elementName" type="datetime-local" value="">' .
            '</label>' .
        '</p>' . "\n";

        $parameters = array();
        $datetimelocal = new datetimelocal('elementName', $parameters, 'formName');
        $this->assertEquals($expected, $datetimelocal->__toString());
    }

    public function testValue() {
        $expected = '<p id="formName-elementName" class="input-datetimelocal" data-errorMessage="Please enter valid data!">' .
            '<label>' .
                '<span class="label">elementName</span>' .
                '<input name="elementName" type="datetime-local" value="7331">' .
            '</label>' .
        '</p>' . "\n";

        $parameters = array();
        $datetimelocal = new datetimelocal('elementName', $parameters, 'formName');
        $datetimelocal->setValue(7331);
        $this->assertEquals($expected, $datetimelocal->__toString());
    }

    public function testRequired() {
        $expected = '<p id="formName-elementName" class="input-datetimelocal required" data-errorMessage="Please enter valid data!">' .
            '<label>' .
                '<span class="label">elementName <em>*</em></span>' .
                '<input name="elementName" type="datetime-local" required value="">' .
            '</label>' .
        '</p>' . "\n";

        $parameters = array();
        $datetimelocal = new datetimelocal('elementName', $parameters, 'formName');
        $datetimelocal->setRequired();
        $this->assertEquals($expected, $datetimelocal->__toString());
    }

    public function testHtmlEscaping() {
        $expected = '<p id="formName-elementName" class="input-datetimelocal required" title="ti&quot;&gt;tle" data-errorMessage="er&quot;&gt;rorMessage">' .
            '<label>' .
                '<span class="label">la&quot;&gt;bel <em>ma&quot;&gt;rker</em></span>' .
                '<input name="elementName" type="datetime-local" required value="">' .
            '</label>' .
        '</p>' . "\n";

        $parameters = array(
            'label'         => 'la">bel',
            'marker'        => 'ma">rker',
            'errorMessage'  => 'er">rorMessage',
            'title'         => 'ti">tle',
            'required'      => true,
        );
        $datetimelocal = new datetimelocal('elementName', $parameters, 'formName');
        $this->assertEquals($expected, $datetimelocal->__toString());
    }
}
