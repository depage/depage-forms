<?php

require_once('../htmlform.php');

use depage\htmlform\htmlform;
use depage\htmlform\exceptions;

class htmlformTest extends PHPUnit_Framework_TestCase {
    public function testDuplicateElementNameException() {
        $this->form = new htmlform('formName');

        $this->form->addHidden('duplicate', array());
        try {
            $this->form->addHidden('duplicate', array());
        } catch (exceptions\duplicateElementNameException $expected) {
            return;
        }
        $this->fail('Expected duplicateElementNameException.');
    }

    public function testToStringSimple() {
    $rendered = '<form id="formName" name="formName" method="post" action="' . $_SERVER['REQUEST_URI'] . '"><input name="formName" id="formName-formName" type="hidden" class="input-hidden" value="formName">' . "\n" . '<p id="formName-submit"><input type="submit" value="submit"></p></form>';
        $form = new htmlform('formName');

        $this->assertEquals($rendered, $form->__toString());
    }

    public function testEmptyFormBeforePostValidation() {
        $_SESSION = array('formName-data' => array());
        $this->form = new htmlform('formName');
        $this->form->process();
        $this->assertEquals(false, $this->form->validate());
    }

    public function testGetElement() {
        $this->form = new htmlform('formName');
        $this->assertEquals('formName', $this->form->getElement('formName')->getName());
        $this->assertEquals(false, $this->form->getElement('bogusInputName'));
    }

    public function testPopulate() {
        $form = new htmlform('formName');
        $text1 = $form->addText('text1Name');
        $text2 = $form->addText('text2Name');

        $form->populate(array('text1Name' => 'text1Value'));

        $this->assertTrue((bool) strpos($text1->__toString(), 'value="text1Value"'));
        $this->assertTrue((bool) strpos($text2->__toString(), 'value=""'));
    }
}
?>
