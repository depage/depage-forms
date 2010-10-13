<?php

require_once('../www/formClass.php');

class formClassTest extends PHPUnit_Framework_TestCase {
    public function testDuplicateInputNameException() {
        $this->form = new formClass('nameString');

        $this->form->addHidden('duplicate' , array());
        try {
            $this->form->addHidden('duplicate' , array());
        }
        catch (duplicateInputNameException $expected) {
            return;
        }
        $this->fail('Expected duplicateInputNameException.');
    }

    public function testFormNameNoStringException() {
        try {
            $form = new formClass(true, array());
        }
        catch (formNameNoStringException $expected) {
            return;
        }
        $this->fail('Expected formNameNoStringException.');
    }

    public function testInvalidFormNameException() {
        try {
            $form = new formClass(' ', array());
        }
        catch (invalidFormNameException $expected) {
            return;
        }
        $this->fail('Expected invalidFormNameException.');
    }

    public function testToStringSimple() {
        $rendered = '<form id="nameString" name="nameString" method="post" action="'.$_SERVER['REQUEST_URI'].'"><input name="form-name" id="nameString-form-name" type="hidden" class="input-hidden" value="nameString"><p id="nameString-submit"><input type="submit" name="submit" value="submit"></p></form>';
        $this->form = new formClass('nameString');

        $this->assertEquals($rendered, $this->form->__toString());
    }

    public function testEmptyFormValidation() {
        $_SESSION = array('nameString-data' => array());
        $this->form = new formClass('nameString');
        $this->form->process();
        $this->assertEquals(true, $this->form->isValid());
    }

    public function testGetInput() {
        $this->form = new formClass('nameString');
        $this->assertEquals('form-name', $this->form->getInput('form-name')->getName());
        $this->assertEquals(false, $this->form->getInput('bogus-input-name'));
    }
}
?>
