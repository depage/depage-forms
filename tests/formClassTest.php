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
        $rendered = '<form id="nameString" name="nameString" method="post"><p id="nameString-form-name" class="input-hidden"><input name="form-name" type="hidden" value="nameString"></p><p id="nameString-submit"><input type="submit" name="submit" value="submit"></p></form>';
        $this->form = new formClass('nameString');

        $this->assertEquals($rendered, $this->form->__toString());
    }

    public function testEmptyFormValidation() {
        $_SESSION = array('nameString-data' => array());
        $this->form = new formClass('nameString');
        $this->form->process();
        $this->assertEquals(true, $this->form->isValid());
    }
}
?>
