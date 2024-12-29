<?php

use PHPUnit\Framework\TestCase;
use Depage\HtmlForm\HtmlForm;
use Depage\HtmlForm\Elements\Step;

/**
 * Tests for step container element rendering.
 **/
class stepToStringTest extends TestCase
{
    protected $form;
    protected $step;

    // {{{ setUp()
    public function setUp(): void
    {
        $this->form = new nameTestForm();
        $this->step = new Step('stepName', [], $this->form);
    }
    // }}}

    // {{{ testSimple()
    /**
     * Element with default setup (empty)
     **/
    public function testSimple()
    {
        $expected = '';
        $this->assertEquals($expected, $this->step->__toString());
    }
    // }}}

    // {{{ testAddFieldset()
    /**
     * With fieldset subelement.
     **/
    public function testAddFieldset()
    {
        $expected = '<fieldset id="formName-fieldsetName" name="fieldsetName">' .
            '<legend><span>fieldsetName</span></legend>' .
        '</fieldset>' . "\n";
        $this->step->addFieldset('fieldsetName');

        $this->assertEquals($expected, $this->step->__toString());
    }
    // }}}

    // {{{ testAddText()
    /**
     * With text subelement
     **/
    public function testAddText()
    {
        $expected = '<p id="formName-textName" class="input-text" data-errorMessage="Please enter valid data">' .
                '<label>' .
                    '<span class="depage-label">textName</span>' .
                    '<input name="textName" type="text" value="">' .
                '</label>' .
            '</p>' . "\n";

        $this->step->addText('textName');
        $this->assertEquals($expected, $this->step->__toString());
    }
    // }}}
}
