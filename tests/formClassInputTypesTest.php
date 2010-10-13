<?php

require_once('../www/formClass.php');

class formClassInputTypesTest extends PHPUnit_Framework_TestCase {
    protected $form;

    protected function setUp() {
        $this->form = new formClass('nameString');
    }

    public function testAddHidden() {
        $this->form->addHidden('nameString');
        $inputs = $this->form->getInputs();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddText() {
        $this->form->addText('nameString');
        $inputs = $this->form->getInputs();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddTextArea() {
        $this->form->addTextArea('nameString');
        $inputs = $this->form->getInputs();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddSearch() {
        $this->form->addSearch('nameString');
        $inputs = $this->form->getInputs();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddUrl() {
        $this->form->addUrl('nameString');
        $inputs = $this->form->getInputs();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddTel() {
        $this->form->addTel('nameString');
        $inputs = $this->form->getInputs();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddPassword() {
        $this->form->addPassword('nameString');
        $inputs = $this->form->getInputs();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddDateTime() {
        $this->form->addDateTime('nameString');
        $inputs = $this->form->getInputs();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddDate() {
        $this->form->addDate('nameString');
        $inputs = $this->form->getInputs();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddMonth() {
        $this->form->addMonth('nameString');
        $inputs = $this->form->getInputs();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddWeek() {
        $this->form->addWeek('nameString');
        $inputs = $this->form->getInputs();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddTime() {
        $this->form->addTime('nameString');
        $inputs = $this->form->getInputs();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddDateTimeLocal() {
        $this->form->addDateTimeLocal('nameString');
        $inputs = $this->form->getInputs();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddNumber() {
        $this->form->addNumber('nameString');
        $inputs = $this->form->getInputs();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddRange() {
        $this->form->addRange('nameString');
        $inputs = $this->form->getInputs();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddColor() {
        $this->form->addColor('nameString');
        $inputs = $this->form->getInputs();
        $this->assertInstanceOf('textClass', $inputs[1]);
    }

    public function testAddCheckbox() {
        $this->form->addCheckbox('nameString');
        $inputs = $this->form->getInputs();
        $this->assertInstanceOf('checkboxClass', $inputs[1]);
    }

    public function testAddRadio() {
        $this->form->addRadio('nameString');
        $inputs = $this->form->getInputs();
        $this->assertInstanceOf('checkboxClass', $inputs[1]);
    }

    public function testAddSelect() {
        $this->form->addSelect('nameString');
        $inputs = $this->form->getInputs();
        $this->assertInstanceOf('checkboxClass', $inputs[1]);
    }

    public function testAddFile() {
        $this->form->addFile('nameString');
        $inputs = $this->form->getInputs();
        $this->assertInstanceOf('fileClass', $inputs[1]);
    }
}
?>
