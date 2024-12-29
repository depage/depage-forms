<?php

use PHPUnit\Framework\TestCase;
use Depage\HtmlForm\HtmlForm;
use Depage\HtmlForm\Elements\Fieldset;

/**
 * Tests for fieldset container element rendering.
 **/
class fieldsetToStringTest extends TestCase
{
    protected $form;
    protected $fieldset;

    // {{{ setUp()
    public function setUp(): void
    {
        $this->form = new nameTestForm('formName');
        $this->fieldset = new Fieldset('fieldsetName', [], $this->form);
    }
    // }}}

    // {{{ testSimple()
    /**
     * Element with default setup
     **/
    public function testSimple()
    {
        $expected = '<fieldset id="formName-fieldsetName" name="fieldsetName">' .
                        '<legend><span>fieldsetName</span></legend>' .
                    '</fieldset>' . "\n";
        $this->assertEquals($expected, $this->fieldset->__toString());
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
            '<fieldset id="formName-secondFieldsetName" name="secondFieldsetName">' .
                '<legend><span>secondFieldsetName</span></legend>' .
            '</fieldset>' . "\n" .
        '</fieldset>' . "\n";
        $this->fieldset->addFieldset('secondFieldsetName');

        $this->assertEquals($expected, $this->fieldset->__toString());
    }
    // }}}

    // {{{ testAddText()
    /**
     * With text subelement
     **/
    public function testAddText()
    {
        $expected = '<fieldset id="formName-fieldsetName" name="fieldsetName">' .
            '<legend><span>fieldsetName</span></legend>' .
            '<p id="formName-textName" class="input-text" data-errorMessage="Please enter valid data">' .
                '<label>' .
                    '<span class="depage-label">textName</span>' .
                    '<input name="textName" type="text" value="">' .
                '</label>' .
            '</p>' . "\n" .
        '</fieldset>' . "\n";

        $this->fieldset->addText('textName');
        $this->assertEquals($expected, $this->fieldset->__toString());
    }
    // }}}
}
