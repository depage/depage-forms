<?php

use depage\htmlform\elements\boolean;

/**
 * Tests for boolean input element rendering.
 **/
class booleanToStringTest extends PHPUnit_Framework_TestCase {
    // {{{ setUp()
    public function setUp() {
        $this->form     = new nameTestForm();
        $this->boolean  = new boolean('booleanName', array(), $this->form);
    }
    // }}}

    // {{{ testSimple()
    /**
     * Element with default setup
     **/
    public function testSimple() {
        $expected = '<p id="formName-booleanName" class="input-boolean" data-errorMessage="Please check this box if you want to proceed">' .
            '<label>' .
                '<input type="checkbox" name="booleanName" value="true">' .
                '<span class="label">booleanName</span>' .
            '</label>' .
        '</p>' . "\n";

        $this->assertEquals($expected, $this->boolean->__toString());
    }
    // }}}

    // {{{ testChecked()
    /**
     * Checked element
     **/
    public function testChecked() {
        $expected = '<p id="formName-booleanName" class="input-boolean" data-errorMessage="Please check this box if you want to proceed">' .
            '<label>' .
                '<input type="checkbox" name="booleanName" value="true" checked="yes">' .
                '<span class="label">booleanName</span>' .
            '</label>' .
        '</p>' . "\n";

        $this->boolean->setValue(true);
        $this->assertEquals($expected, $this->boolean->__toString());
    }
    // }}}

    // {{{ testRequired()
    /**
     * Rendered element with set required attribute
     **/
    public function testRequired() {
        $expected = '<p id="formName-booleanName" class="input-boolean required" data-errorMessage="Please check this box if you want to proceed">' .
            '<label>' .
                '<input type="checkbox" name="booleanName" required="required" value="true">' .
                '<span class="label">booleanName <em>*</em></span>' .
            '</label>' .
        '</p>' . "\n";

        $this->boolean->setRequired();
        $this->assertEquals($expected, $this->boolean->__toString());
    }
    // }}}

    // {{{ testHtmlEscaping()
    /**
     * Tests html escaping of attributes that can be set by instantiation parameters
     **/
    public function testHtmlEscaping() {
        $expected = '<p id="formName-booleanName" class="input-boolean required" title="ti&quot;&gt;tle" data-errorMessage="er&quot;&gt;rorMessage">' .
            '<label>' .
                '<input type="checkbox" name="booleanName" required="required" value="true">' .
                '<span class="label">la&quot;&gt;bel <em>ma&quot;&gt;rker</em></span>' .
            '</label>' .
        '</p>' . "\n";

        $parameters = array(
            'label'         => 'la">bel',
            'marker'        => 'ma">rker',
            'errorMessage'  => 'er">rorMessage',
            'title'         => 'ti">tle',
            'required'      => true,
        );
        $boolean = new boolean('booleanName', $parameters, $this->form);
        $this->assertEquals($expected, $boolean->__toString());
    }
    // }}}
}
