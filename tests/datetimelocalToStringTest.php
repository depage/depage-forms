<?php

use depage\htmlform\elements\datetimelocal;

class datetimelocalToStringTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->form = new nameTestForm;
        $parameters = array();
        $this->datetimelocal = new datetimelocal('datetimelocalName', $parameters, $this->form);
    }

    public function testSimple() {
        $expected = '<p id="formName-datetimelocalName" class="input-datetimelocal" data-errorMessage="Please enter valid data!">' .
            '<label>' .
                '<span class="label">datetimelocalName</span>' .
                '<input name="datetimelocalName" type="datetime-local" value="">' .
            '</label>' .
        '</p>' . "\n";

        $this->assertEquals($expected, $this->datetimelocal->__toString());
    }

    public function testValue() {
        $expected = '<p id="formName-datetimelocalName" class="input-datetimelocal" data-errorMessage="Please enter valid data!">' .
            '<label>' .
                '<span class="label">datetimelocalName</span>' .
                '<input name="datetimelocalName" type="datetime-local" value="7331">' .
            '</label>' .
        '</p>' . "\n";

        $this->datetimelocal->setValue(7331);
        $this->assertEquals($expected, $this->datetimelocal->__toString());
    }

    public function testRequired() {
        $expected = '<p id="formName-datetimelocalName" class="input-datetimelocal required" data-errorMessage="Please enter valid data!">' .
            '<label>' .
                '<span class="label">datetimelocalName <em>*</em></span>' .
                '<input name="datetimelocalName" type="datetime-local" required value="">' .
            '</label>' .
        '</p>' . "\n";

        $this->datetimelocal->setRequired();
        $this->assertEquals($expected, $this->datetimelocal->__toString());
    }

    public function testHtmlEscaping() {
        $expected = '<p id="formName-datetimelocalName" class="input-datetimelocal required" title="ti&quot;&gt;tle" data-errorMessage="er&quot;&gt;rorMessage">' .
            '<label>' .
                '<span class="label">la&quot;&gt;bel <em>ma&quot;&gt;rker</em></span>' .
                '<input name="datetimelocalName" type="datetime-local" required value="">' .
            '</label>' .
        '</p>' . "\n";

        $parameters = array(
            'label'         => 'la">bel',
            'marker'        => 'ma">rker',
            'errorMessage'  => 'er">rorMessage',
            'title'         => 'ti">tle',
            'required'      => true,
        );
        $datetimelocal = new datetimelocal('datetimelocalName', $parameters, $this->form);
        $this->assertEquals($expected, $datetimelocal->__toString());
    }
}
