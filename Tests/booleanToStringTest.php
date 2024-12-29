<?php

use PHPUnit\Framework\TestCase;
use Depage\HtmlForm\Elements\Boolean;

/**
 * Tests for boolean input element rendering.
 **/
class booleanToStringTest extends TestCase
{
    protected $form;
    protected $boolean;

    // {{{ setUp()
    public function setUp(): void
    {
        $this->form     = new nameTestForm();
        $this->boolean  = new Boolean('booleanName', [], $this->form);
    }
    // }}}

    // {{{ testSimple()
    /**
     * Element with default setup
     **/
    public function testSimple()
    {
        $expected = '<p id="formName-booleanName" class="input-boolean" data-errorMessage="Please check this box if you want to proceed">' .
            '<label>' .
                '<input type="checkbox" name="booleanName" value="true">' .
                '<span class="depage-label">booleanName</span>' .
            '</label>' .
        '</p>' . "\n";

        $this->assertEquals($expected, $this->boolean->__toString());
    }
    // }}}

    // {{{ testChecked()
    /**
     * Checked element
     **/
    public function testChecked()
    {
        $expected = '<p id="formName-booleanName" class="input-boolean" data-errorMessage="Please check this box if you want to proceed">' .
            '<label>' .
                '<input type="checkbox" name="booleanName" value="true" checked="yes">' .
                '<span class="depage-label">booleanName</span>' .
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
    public function testRequired()
    {
        $expected = '<p id="formName-booleanName" class="input-boolean required" data-errorMessage="Please check this box if you want to proceed">' .
            '<label>' .
                '<input type="checkbox" name="booleanName" required="required" value="true">' .
                '<span class="depage-label">booleanName <em>*</em></span>' .
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
    public function testHtmlEscaping()
    {
        $expected = '<p id="formName-booleanName" class="input-boolean required" title="ti&quot;&gt;tle" data-errorMessage="er&quot;&gt;rorMessage">' .
            '<label>' .
                '<input type="checkbox" name="booleanName" required="required" value="true">' .
                '<span class="depage-label">la&quot;&gt;bel <em>ma&quot;&gt;rker</em></span>' .
            '</label>' .
        '</p>' . "\n";

        $parameters = [
            'label'         => 'la">bel',
            'marker'        => 'ma">rker',
            'errorMessage'  => 'er">rorMessage',
            'title'         => 'ti">tle',
            'required'      => true,
        ];
        $boolean = new boolean('booleanName', $parameters, $this->form);
        $this->assertEquals($expected, $boolean->__toString());
    }
    // }}}
}
