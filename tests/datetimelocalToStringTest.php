<?php

use depage\htmlform\elements\datetimelocal;

/**
 * Tests for datetime-local input element rendering.
 **/
class datetimelocalToStringTest extends PHPUnit_Framework_TestCase {
    // {{{ setUp
    public function setUp() {
        $this->form = new nameTestForm;
        $this->datetimelocal = new datetimelocal('datetimelocalName', array(), $this->form);
    }
    // }}}

    // {{{ testSimple()
    /**
     * Element with default setup
     **/
    public function testSimple() {
        $expected = '<p id="formName-datetimelocalName" class="input-datetimelocal" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="label">datetimelocalName</span>' .
                '<input name="datetimelocalName" type="datetime-local" value="">' .
            '</label>' .
        '</p>' . "\n";

        $this->assertEquals($expected, $this->datetimelocal->__toString());
    }
    // }}}

    // {{{ testValue()
    /**
     * Tests rendering with value set.
     **/
    public function testValue() {
        $expected = '<p id="formName-datetimelocalName" class="input-datetimelocal" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="label">datetimelocalName</span>' .
                '<input name="datetimelocalName" type="datetime-local" value="7331">' .
            '</label>' .
        '</p>' . "\n";

        $this->datetimelocal->setValue(7331);
        $this->assertEquals($expected, $this->datetimelocal->__toString());
    }
    // }}}

    // {{{ testRequired()
    /**
     * Rendered element with set required attribute
     **/
    public function testRequired() {
        $expected = '<p id="formName-datetimelocalName" class="input-datetimelocal required" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="label">datetimelocalName <em>*</em></span>' .
                '<input name="datetimelocalName" type="datetime-local" required="required" value="">' .
            '</label>' .
        '</p>' . "\n";

        $this->datetimelocal->setRequired();
        $this->assertEquals($expected, $this->datetimelocal->__toString());
    }
    // }}}

    // {{{ testHtmlEscaping()
    /**
     * Tests html escaping of attributes that can be set by instantiation parameters
     **/
    public function testHtmlEscaping() {
        $expected = '<p id="formName-datetimelocalName" class="input-datetimelocal required" title="ti&quot;&gt;tle" data-errorMessage="er&quot;&gt;rorMessage">' .
            '<label>' .
                '<span class="label">la&quot;&gt;bel <em>ma&quot;&gt;rker</em></span>' .
                '<input name="datetimelocalName" type="datetime-local" required="required" value="">' .
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
    // }}}
}
