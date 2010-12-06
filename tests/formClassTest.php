<?php

require_once('../www/formClass.php');

class formClassTest extends PHPUnit_Framework_TestCase {
    public function testDuplicateElementNameException() {
        $this->form = new formClass('nameString');

        $this->form->addHidden('duplicate', array());
        try {
            $this->form->addHidden('duplicate', array());
        }
        catch (duplicateElementNameException $expected) {
            return;
        }
        $this->fail('Expected duplicateElementNameException.');
    }

    public function testContainerNameNoStringException() {
        try {
            $form = new formClass(true, array());
        }
        catch (containerNameNoStringException $expected) {
            return;
        }
        $this->fail('Expected containerNameNoStringException.');
    }

    public function testInvalidContainerNameException() {
        try {
            $form = new formClass(' ', array());
        }
        catch (invalidContainerNameException $expected) {
            return;
        }
        $this->fail('Expected invalidFormNameException.');
    }

    public function testToStringSimple() {
    $rendered = '<form id="nameString" name="nameString" method="post" action="' . $_SERVER['REQUEST_URI'] . '"><input name="form-name" id="nameString-form-name" type="hidden" class="input-hidden" value="nameString">
<p id="nameString-submit"><input type="submit" name="submit" value="submit"></p></form>';
        $form = new formClass('nameString');

        $this->assertEquals($rendered, $form->__toString());
    }

    public function testEmptyFormValidation() {
        $_SESSION = array('nameString-data' => array());
        $this->form = new formClass('nameString');
        $this->form->process();
        $this->assertEquals(true, $this->form->isValid());
    }

    public function testGetElement() {
        $this->form = new formClass('nameString');
        $this->assertEquals('form-name', $this->form->getElement('form-name')->getName());
        $this->assertEquals(false, $this->form->getInput('bogus-input-name'));
    }
}
?>
