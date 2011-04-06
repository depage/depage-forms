<?php

require_once('../htmlform.php');

use depage\htmlform\htmlform;
use depage\htmlform\exceptions;

class htmlformTest extends PHPUnit_Framework_TestCase {
    public function testDuplicateElementNameException() {
        $this->form = new htmlform('formName');

        $this->form->addHidden('duplicate', array());
        try {
            $this->form->addFieldset('duplicate', array());
        } catch (exceptions\duplicateElementNameException $expected) {
            return;
        }
        $this->fail('Expected duplicateElementNameException.');
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

        // populate() only sets (protected) default value. The easiest way to check is to render it.
        $expectedText1 = '<p id="formName-text1Name" class="input-text" data-errorMessage="Please enter valid data!">' .
            '<label>' .
                '<span class="label">text1Name</span>' .
                '<input name="text1Name" type="text" value="text1Value">' .
            '</label>' .
        '</p>' . "\n";
        $expectedText2 = '<p id="formName-text2Name" class="input-text" data-errorMessage="Please enter valid data!">' .
            '<label>' .
                '<span class="label">text2Name</span>' .
                '<input name="text2Name" type="text" value="">' .
            '</label>' .
        '</p>' . "\n";

        $this->assertEquals($expectedText1, $text1->__toString());
        $this->assertEquals($expectedText2, $text2->__toString());
    }

    public function testAddStep() {
        $form = new htmlform('formName');
        $step = $form->addStep('stepName');

        $this->assertEquals('stepName', $step->getName());
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\step', $step);
    }

    /**
     * Since getCurrentElements() is private we need to go through
     * inCurrentStep() and updateInputValue() in order to test it.
     *
     * This test is set up so that the update call happens when we're in the
     * right step (1). Hence, the element value should change to 'text0Value'.
     **/
    public function testGetCurrentElementsInStep() {
        $_POST['formName']  = 'formName';
        $_POST['text0Name'] = 'text0Value';
        $_GET['step']       = '1';

        $form   = new htmlform('formName');
        $step0  = $form->addStep('step0');
        $step1  = $form->addStep('step1');
        $text0  = $step1->addText('text0Name');

        $form->updateInputValue('text0Name');

        $this->assertEquals('text0Value', $text0->getValue());
    }

    /**
     * Since getCurrentElements() is private we need to go through
     * inCurrentStep() and updateInputValue() in order to test it.
     *
     * This test is set up so that the update call happens when we're not in
     * the current step, so the initial value shouldn't change.
     **/
    public function testGetCurrentElementsNotInStep() {
        $_POST['formName']  = 'formName';
        $_POST['text0Name'] = 'text0Value';
        $_GET['step']       = '0';

        $form   = new htmlform('formName');
        $step0  = $form->addStep('step0');
        $step1  = $form->addStep('step1');
        $text0  = $step1->addText('text0Name');

        $form->updateInputValue('text0Name');

        $this->assertNull($text0->getValue());
    }

    public function testIsEmpty() {
        $form = new htmlform('formName');
        $this->assertTrue($form->isEmpty());

        $_SESSION['formName-data']['formName'] = 'formName';
        $this->assertFalse($form->isEmpty());
    }
}
?>
