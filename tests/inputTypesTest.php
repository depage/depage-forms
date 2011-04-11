<?php

use depage\htmlform\htmlform;

/**
 * Tests availability of input element types.
 **/
class inputTypesTest extends PHPUnit_Framework_TestCase {
    protected function setUp() {
        $this->form = new htmlform('formNameString');
    }

    public function testAddHidden() {
        $this->form->addHidden('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\hidden', $element);
    }

    public function testAddText() {
        $this->form->addText('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\text', $element);
    }

    public function testAddTextArea() {
        $this->form->addTextArea('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\textarea', $element);
    }

    public function testAddSearch() {
        $this->form->addSearch('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\search', $element);
    }

    public function testAddUrl() {
        $this->form->addUrl('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\url', $element);
    }

    public function testAddTel() {
        $this->form->addTel('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\tel', $element);
    }

    public function testAddPassword() {
        $this->form->addPassword('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\password', $element);
    }

    public function testAddDateTime() {
        $this->form->addDateTime('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\datetime', $element);
    }

    public function testAddDate() {
        $this->form->addDate('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\date', $element);
    }

    public function testAddMonth() {
        $this->form->addMonth('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\month', $element);
    }

    public function testAddWeek() {
        $this->form->addWeek('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\week', $element);
    }

    public function testAddTime() {
        $this->form->addTime('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\time', $element);
    }

    public function testAddDateTimeLocal() {
        $this->form->addDateTimeLocal('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\datetimelocal', $element);
    }

    public function testAddNumber() {
        $this->form->addNumber('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\number', $element);
    }

    public function testAddRange() {
        $this->form->addRange('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\range', $element);
    }
    public function testAddColor() {
        $this->form->addColor('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\color', $element);
    }

    public function testAddBoolean() {
        $this->form->addBoolean('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\boolean', $element);
    }

    public function testAddSingle() {
        $this->form->addSingle('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\single', $element);
    }

    public function testAddMultiple() {
        $this->form->addMultiple('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\multiple', $element);
    }

    public function testAddFile() {
        $this->form->addFile('nameString');
        $element = $this->form->getElement('nameString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\file', $element);
    }
}
?>
