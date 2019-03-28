<?php

use PHPUnit\Framework\TestCase;
use Depage\HtmlForm\HtmlForm;

/**
 * Tests availability of input element types.
 **/
class inputTypesTest extends TestCase
{
    // {{{ setUp()
    protected function setUp():void
    {
        $this->form = new HtmlForm('formNameString');
    }
    // }}}

    // {{{ testAddHidden()
    public function testAddHidden()
    {
        $this->form->addHidden('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\Depage\\HtmlForm\\Elements\\Hidden', $element);
    }
    // }}}

    // {{{ testAddText()
    public function testAddText()
    {
        $this->form->addText('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\Depage\\HtmlForm\\Elements\\Text', $element);
    }
    // }}}

    // {{{ testAddTextArea()
    public function testAddTextArea()
    {
        $this->form->addTextarea('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\Depage\\HtmlForm\\Elements\\Textarea', $element);
    }
    // }}}

    // {{{ testAddSearch()
    public function testAddSearch()
    {
        $this->form->addSearch('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\Depage\\HtmlForm\\Elements\\Search', $element);
    }
    // }}}

    // {{{ testAddUrl()
    public function testAddUrl()
    {
        $this->form->addUrl('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\Depage\\HtmlForm\\Elements\\Url', $element);
    }
    // }}}

    // {{{ testAddTel()
    public function testAddTel()
    {
        $this->form->addTel('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\Depage\\HtmlForm\\Elements\\Tel', $element);
    }
    // }}}

    // {{{ testAddPassword()
    public function testAddPassword()
    {
        $this->form->addPassword('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\Depage\\HtmlForm\\Elements\\Password', $element);
    }
    // }}}

    // {{{ testAddDateTime()
    public function testAddDatetime()
    {
        $this->form->addDatetime('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\Depage\\HtmlForm\\Elements\\Datetime', $element);
    }
    // }}}

    // {{{ testAddDate()
    public function testAddDate()
    {
        $this->form->addDate('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\Depage\\HtmlForm\\Elements\\Date', $element);
    }
    // }}}

    // {{{ testAddMonth()
    public function testAddMonth()
    {
        $this->form->addMonth('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\Depage\\HtmlForm\\Elements\\Month', $element);
    }
    // }}}

    // {{{ testAddWeek()
    public function testAddWeek()
    {
        $this->form->addWeek('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\Depage\\HtmlForm\\Elements\\Week', $element);
    }
    // }}}

    // {{{ testAddTime()
    public function testAddTime()
    {
        $this->form->addTime('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\Depage\\HtmlForm\\Elements\\Time', $element);
    }
    // }}}

    // {{{ testAddDateTimeLocal()
    public function testAddDateTimeLocal()
    {
        $this->form->addDateTimeLocal('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\Depage\\HtmlForm\\Elements\\Datetimelocal', $element);
    }
    // }}}

    // {{{ testAddNumber()
    public function testAddNumber()
    {
        $this->form->addNumber('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\Depage\\HtmlForm\\Elements\\Number', $element);
    }
    // }}}

    // {{{ testAddRange()
    public function testAddRange()
    {
        $this->form->addRange('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\Depage\\HtmlForm\\Elements\\Range', $element);
    }
    // }}}

    // {{{ testAddColor()
    public function testAddColor()
    {
        $this->form->addColor('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\Depage\\HtmlForm\\Elements\\Color', $element);
    }
    // }}}

    // {{{ testAddBoolean()
    public function testAddBoolean()
    {
        $this->form->addBoolean('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\Depage\\HtmlForm\\Elements\\Boolean', $element);
    }
    // }}}

    // {{{ testAddSingle()
    public function testAddSingle()
    {
        $this->form->addSingle('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\Depage\\HtmlForm\\Elements\\Single', $element);
    }
    // }}}

    // {{{ testAddMultiple()
    public function testAddMultiple()
    {
        $this->form->addMultiple('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\Depage\\HtmlForm\\Elements\\Multiple', $element);
    }
    // }}}

    // {{{ testAddFile()
    public function testAddFile()
    {
        $this->form->addFile('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\Depage\\HtmlForm\\Elements\\File', $element);
    }
    // }}}
}
