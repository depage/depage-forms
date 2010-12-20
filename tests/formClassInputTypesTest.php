<?php

require_once('../www/formClass.php');

class formClassInputTypesTest extends PHPUnit_Framework_TestCase {
    protected $form;

    protected function setUp() {
        $this->form = new formClass('nameString');
    }

    public function testAddHidden() {
        $this->form->addHidden('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddText() {
        $this->form->addText('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddTextArea() {
        $this->form->addTextArea('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddSearch() {
        $this->form->addSearch('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddUrl() {
        $this->form->addUrl('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddTel() {
        $this->form->addTel('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddPassword() {
        $this->form->addPassword('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddDateTime() {
        $this->form->addDateTime('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddDate() {
        $this->form->addDate('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddMonth() {
        $this->form->addMonth('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddWeek() {
        $this->form->addWeek('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddTime() {
        $this->form->addTime('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddDateTimeLocal() {
        $this->form->addDateTimeLocal('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddNumber() {
        $this->form->addNumber('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddRange() {
        $this->form->addRange('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddColor() {
        $this->form->addColor('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddBoolean() {
        $this->form->addBoolean('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('elementBoolean', $inputs[1]);
    }

    public function testAddSingle() {
        $this->form->addSingle('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('elementSingle', $inputs[1]);
    }

    public function testAddMultiple() {
        $this->form->addMultiple('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('elementMultiple', $inputs[1]);
    }

    public function testAddFile() {
        $this->form->addFile('nameString');
        $inputs = $this->form->getElements();
        $this->assertInstanceOf('elementFile', $inputs[1]);
    }
}
?>
