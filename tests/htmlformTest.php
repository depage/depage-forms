<?php

require_once('../htmlform.php');

use depage\htmlform\htmlform;
use depage\htmlform\exceptions;

class htmlformTest extends PHPUnit_Framework_TestCase {
    public function testDuplicateElementNameException() {
        $this->form = new htmlform('nameString');

        $this->form->addHidden('duplicate', array());
        try {
            $this->form->addHidden('duplicate', array());
        } catch (exceptions\duplicateElementNameException $expected) {
            return;
        }
        $this->fail('Expected duplicateElementNameException.');
    }

    public function testContainerNameNoStringException() {
        try {
            $form = new htmlform(true, array());
        } catch (exceptions\containerNameNoStringException $expected) {
            return;
        }
        $this->fail('Expected containerNameNoStringException.');
    }

    public function testInvalidContainerNameException() {
        try {
            $form = new htmlform(' ', array());
        } catch (exceptions\invalidContainerNameException $expected) {
            return;
        }
        $this->fail('Expected invalidFormNameException.');
    }

    public function testToStringSimple() {
    $rendered = '<form id="nameString" name="nameString" method="post" action="' . $_SERVER['REQUEST_URI'] . '"><input name="form-name" id="nameString-form-name" type="hidden" class="input-hidden" value="nameString">
<p id="nameString-submit"><input type="submit" name="submit" value="submit"></p></form>';
        $form = new htmlform('nameString');

        $this->assertEquals($rendered, $form->__toString());
    }

    public function testEmptyFormBeforePostValidation() {
        $_SESSION = array('nameString-data' => array());
        $this->form = new htmlform('nameString');
        $this->form->process();
        $this->assertEquals(false, $this->form->isValid());
    }

    public function testGetElement() {
        $this->form = new htmlform('nameString');
        $this->assertEquals('form-name', $this->form->getElement('form-name')->getName());
        $this->assertEquals(false, $this->form->getInput('bogus-input-name'));
    }
}
?>
