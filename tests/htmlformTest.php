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

    public function testToStringSimple() {
    $rendered = '<form id="nameString" name="nameString" method="post" action="' . $_SERVER['REQUEST_URI'] . '"><input name="formName" id="nameString-formName" type="hidden" class="input-hidden" value="nameString">
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
        $this->assertEquals('formName', $this->form->getElement('formName')->getName());
        $this->assertEquals(false, $this->form->getInput('bogusInputName'));
    }
}
?>
