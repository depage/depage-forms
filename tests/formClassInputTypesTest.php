<?php

require_once('../www/formClass.php');

class formClassInputTypesTest extends PHPUnit_Framework_TestCase {
    protected $form;
    
    protected function setUp() {
        $this->form = new formClass('nameString');
    }

    public function testAddHidden() {
        $this->form->addHidden('nameString');
        $this->assertInstanceOf('textClass', $this->form->inputs[0]);
    }

    public function testAddText() {
        $this->form->addText('nameString');
        $this->assertInstanceOf('textClass', $this->form->inputs[0]);
    }

    public function testAddTextArea() {
        $this->form->addTextArea('nameString');
        $this->assertInstanceOf('textClass', $this->form->inputs[0]);
    }

    public function testAddSearch() {
        $this->form->addSearch('nameString');
        $this->assertInstanceOf('textClass', $this->form->inputs[0]);
    }

    public function testAddUrl() {
        $this->form->addUrl('nameString');
        $this->assertInstanceOf('textClass', $this->form->inputs[0]);
    }

    public function testAddTelephone() {
        $this->form->addTelephone('nameString');
        $this->assertInstanceOf('textClass', $this->form->inputs[0]);
    }

    public function testAddPassword() {
        $this->form->addPassword('nameString');
        $this->assertInstanceOf('textClass', $this->form->inputs[0]);
    }

    public function testAddDateTime() {
        $this->form->addDateTime('nameString');
        $this->assertInstanceOf('textClass', $this->form->inputs[0]);
    }

    public function testAddDate() {
        $this->form->addDate('nameString');
        $this->assertInstanceOf('textClass', $this->form->inputs[0]);
    }

    public function testAddMonth() {
        $this->form->addMonth('nameString');
        $this->assertInstanceOf('textClass', $this->form->inputs[0]);
    }

    public function testAddWeek() {
        $this->form->addWeek('nameString');
        $this->assertInstanceOf('textClass', $this->form->inputs[0]);
    }

    public function testAddTime() {
        $this->form->addTime('nameString');
        $this->assertInstanceOf('textClass', $this->form->inputs[0]);
    }

    public function testAddDateTimeLocal() {
        $this->form->addDateTimeLocal('nameString');
        $this->assertInstanceOf('textClass', $this->form->inputs[0]);
    }

    public function testAddNumber() {
        $this->form->addNumber('nameString');
        $this->assertInstanceOf('textClass', $this->form->inputs[0]);
    }

    public function testAddRange() {
        $this->form->addRange('nameString');
        $this->assertInstanceOf('textClass', $this->form->inputs[0]);
    }

    public function testAddColor() {
        $this->form->addColor('nameString');
        $this->assertInstanceOf('textClass', $this->form->inputs[0]);
    }

    public function testAddCheckbox() {
        $this->form->addCheckbox('nameString');
        $this->assertInstanceOf('checkboxClass', $this->form->inputs[0]);
    }

    public function testAddRadio() {
        $this->form->addRadio('nameString');
        $this->assertInstanceOf('checkboxClass', $this->form->inputs[0]);
    }

    public function testAddSelect() {
        $this->form->addSelect('nameString');
        $this->assertInstanceOf('checkboxClass', $this->form->inputs[0]);
    }

    public function testAddFile() {
        $this->form->addFile('nameString');
        $this->assertInstanceOf('fileClass', $this->form->inputs[0]);
    }
}
?>
