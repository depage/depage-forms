<?php

use depage\htmlform\htmlform;

/**
 * Tests availability of input element types.
 **/
class inputTypesTest extends PHPUnit_Framework_TestCase {
    // {{{ setUp()
    protected function setUp() {
        $this->form = new htmlform('formNameString');
    }
    // }}}

    // {{{ testAddHidden()
    public function testAddHidden() {
        $this->form->addHidden('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\hidden', $element);
    }
    // }}}

    // {{{ testAddText()
    public function testAddText() {
        $this->form->addText('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\text', $element);
    }
    // }}}

    // {{{ testAddTextArea()
    public function testAddTextArea() {
        $this->form->addTextArea('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\textarea', $element);
    }
    // }}}

    // {{{ testAddSearch()
    public function testAddSearch() {
        $this->form->addSearch('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\search', $element);
    }
    // }}}

    // {{{ testAddUrl()
    public function testAddUrl() {
        $this->form->addUrl('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\url', $element);
    }
    // }}}

    // {{{ testAddTel()
    public function testAddTel() {
        $this->form->addTel('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\tel', $element);
    }
    // }}}

    // {{{ testAddPassword()
    public function testAddPassword() {
        $this->form->addPassword('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\password', $element);
    }
    // }}}

    // {{{ testAddDateTime()
    public function testAddDateTime() {
        $this->form->addDateTime('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\datetime', $element);
    }
    // }}}

    // {{{ testAddDate()
    public function testAddDate() {
        $this->form->addDate('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\date', $element);
    }
    // }}}

    // {{{ testAddMonth()
    public function testAddMonth() {
        $this->form->addMonth('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\month', $element);
    }
    // }}}

    // {{{ testAddWeek()
    public function testAddWeek() {
        $this->form->addWeek('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\week', $element);
    }
    // }}}

    // {{{ testAddTime()
    public function testAddTime() {
        $this->form->addTime('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\time', $element);
    }
    // }}}

    // {{{ testAddDateTimeLocal()
    public function testAddDateTimeLocal() {
        $this->form->addDateTimeLocal('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\datetimelocal', $element);
    }
    // }}}

    // {{{ testAddNumber()
    public function testAddNumber() {
        $this->form->addNumber('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\number', $element);
    }
    // }}}

    // {{{ testAddRange()
    public function testAddRange() {
        $this->form->addRange('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\range', $element);
    }
    // }}}

    // {{{ testAddColor()
    public function testAddColor() {
        $this->form->addColor('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\color', $element);
    }
    // }}}

    // {{{ testAddBoolean()
    public function testAddBoolean() {
        $this->form->addBoolean('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\boolean', $element);
    }
    // }}}

    // {{{ testAddSingle()
    public function testAddSingle() {
        $this->form->addSingle('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\single', $element);
    }
    // }}}

    // {{{ testAddMultiple()
    public function testAddMultiple() {
        $this->form->addMultiple('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\multiple', $element);
    }
    // }}}

    // {{{ testAddFile()
    public function testAddFile() {
        $this->form->addFile('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\file', $element);
    }
    // }}}
}
