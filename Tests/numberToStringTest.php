<?php

use PHPUnit\Framework\TestCase;
use Depage\HtmlForm\Elements\Number;

/**
 * Tests for boolean input element rendering.
 **/
class numberToStringTest extends TestCase
{
    protected $form;
    protected $number;

    // {{{ setUp()
    public function setUp(): void
    {
        $this->form     = new nameTestForm();
        $this->number   = new Number('numberName', [], $this->form);
    }
    // }}}

    // {{{ testSimple()
    /**
     * Element with default setup
     **/
    public function testSimple()
    {
        $expected = '<p id="formName-numberName" class="input-number" data-errorMessage="Please enter a valid number">' .
            '<label>' .
                '<span class="depage-label">numberName</span>' .
                '<input name="numberName" type="number" value="0">' .
            '</label>' .
        '</p>' . "\n";

        $this->assertEquals($expected, $this->number->__toString());
    }
    // }}}

    // {{{ testValue()
    /**
     * Tests rendering with value set.
     **/
    public function testValue()
    {
        $expected = '<p id="formName-numberName" class="input-number" data-errorMessage="Please enter a valid number">' .
            '<label>' .
                '<span class="depage-label">numberName</span>' .
                '<input name="numberName" type="number" value="7331">' .
            '</label>' .
        '</p>' . "\n";

        $this->number->setValue(7331);
        $this->assertEquals($expected, $this->number->__toString());
    }
    // }}}

    // {{{ testRequired()
    /**
     * Rendered element with set required attribute
     **/
    public function testRequired()
    {
        $expected = '<p id="formName-numberName" class="input-number required" data-errorMessage="Please enter a valid number">' .
            '<label>' .
                '<span class="depage-label">numberName <em>*</em></span>' .
                '<input name="numberName" type="number" required="required" value="0">' .
            '</label>' .
        '</p>' . "\n";

        $this->number->setRequired();
        $this->assertEquals($expected, $this->number->__toString());
    }
    // }}}

    // {{{ testMinMax()
    /**
     * Rendered element with set required attribute
     **/
    public function testMinMax()
    {
        $expected = '<p id="formName-numberName" class="input-number required" data-errorMessage="Please enter a valid number">' .
            '<label>' .
                '<span class="depage-label">numberName <em>*</em></span>' .
                '<input name="numberName" type="number" max="150" min="0" step="2" required="required" value="0">' .
            '</label>' .
        '</p>' . "\n";

        $number = new number('numberName', [
            "min" => 0,
            "max" => 150,
            "step" => 2,
            "required" => true,
        ], $this->form);

        $this->assertEquals($expected, $number->__toString());
    }
    // }}}


    // {{{ testHtmlEscaping()
    /**
     * Tests html escaping of attributes that can be set by instantiation parameters
     **/
    public function testHtmlEscaping()
    {
        $expected = '<p id="formName-numberName" class="input-number required" title="ti&quot;&gt;tle" data-errorMessage="er&quot;&gt;rorMessage">' .
            '<label>' .
                '<span class="depage-label">la&quot;&gt;bel <em>ma&quot;&gt;rker</em></span>' .
                '<input name="numberName" type="number" required="required" value="0">' .
            '</label>' .
        '</p>' . "\n";

        $parameters = [
            'label'         => 'la">bel',
            'marker'        => 'ma">rker',
            'errorMessage'  => 'er">rorMessage',
            'title'         => 'ti">tle',
            'required'      => true,
        ];
        $number = new number('numberName', $parameters, $this->form);
        $this->assertEquals($expected, $number->__toString());
    }
    // }}}
}
