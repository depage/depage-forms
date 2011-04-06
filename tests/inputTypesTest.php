<?php

use depage\htmlform\htmlform;

class inputTypesTest extends PHPUnit_Framework_TestCase {
    protected function setUp() {
        $this->form = new htmlform('formNameString');
    }

    public function testAddHidden() {
        $this->form->addHidden('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\text', $inputs[1]);
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\hidden', $inputs[1]);
    }

    public function testAddText() {
        $this->form->addText('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\text', $inputs[1]);
    }

    public function testAddTextArea() {
        $this->form->addTextArea('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\text', $inputs[1]);
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\textarea', $inputs[1]);
    }

    public function testAddSearch() {
        $this->form->addSearch('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\text', $inputs[1]);
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\search', $inputs[1]);
    }

    public function testAddUrl() {
        $this->form->addUrl('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\text', $inputs[1]);
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\url', $inputs[1]);
    }

    public function testAddTel() {
        $this->form->addTel('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\text', $inputs[1]);
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\tel', $inputs[1]);
    }

    public function testAddPassword() {
        $this->form->addPassword('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\text', $inputs[1]);
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\password', $inputs[1]);
    }

    public function testAddDateTime() {
        $this->form->addDateTime('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\text', $inputs[1]);
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\datetime', $inputs[1]);
    }

    public function testAddDate() {
        $this->form->addDate('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\text', $inputs[1]);
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\date', $inputs[1]);
    }

    public function testAddMonth() {
        $this->form->addMonth('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\text', $inputs[1]);
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\month', $inputs[1]);
    }

    public function testAddWeek() {
        $this->form->addWeek('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\text', $inputs[1]);
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\week', $inputs[1]);
    }

    public function testAddTime() {
        $this->form->addTime('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\text', $inputs[1]);
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\time', $inputs[1]);
    }

    public function testAddDateTimeLocal() {
        $this->form->addDateTimeLocal('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\text', $inputs[1]);
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\datetimelocal', $inputs[1]);
    }

    public function testAddNumber() {
        $this->form->addNumber('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\text', $inputs[1]);
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\number', $inputs[1]);
    }

    public function testAddRange() {
        $this->form->addRange('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\text', $inputs[1]);
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\range', $inputs[1]);
    }
    public function testAddColor() {
        $this->form->addColor('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\text', $inputs[1]);
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\color', $inputs[1]);
    }

    public function testAddBoolean() {
        $this->form->addBoolean('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\boolean', $inputs[1]);
    }

    public function testAddSingle() {
        $this->form->addSingle('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\single', $inputs[1]);
    }

    public function testAddMultiple() {
        $this->form->addMultiple('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\multiple', $inputs[1]);
    }

    public function testAddFile() {
        $this->form->addFile('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\file', $inputs[1]);
    }
}
?>
