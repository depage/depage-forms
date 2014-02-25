<?php

use depage\htmlform\htmlform;
use depage\htmlform\exceptions;

/**
 * General tests for the htmlform class.
 **/
class htmlformTest extends PHPUnit_Framework_TestCase
{
    // {{{ testDuplicateElementNameException()
    /**
     * Throw exception when subelements have the same name.
     **/
    public function testDuplicateElementNameException()
    {
        $this->form = new htmlform('formName');

        $this->form->addFieldset('duplicate');
        try {
            $this->form->addHidden('duplicate');
        } catch (exceptions\duplicateElementNameException $expected) {
            return;
        }
        $this->fail('Expected duplicateElementNameException.');
    }
    // }}}

    // {{{ testEmptyFormBeforePostValidation()
    /**
     * Unsubmitted forms have to be invalid, so we don't redirect to success
     * page right away.
     **/
    public function testEmptyFormBeforePostValidation()
    {
        $_SESSION = array('formName-data' => array());
        $this->form = new htmlform('formName');
        $this->form->process();
        $this->assertFalse($this->form->validate());
    }
    // }}}

    // {{{ testPopulate()
    /**
     * "Populating" the forms subelements with values.
     **/
    public function testPopulate()
    {
        $form = new htmlform('formName');
        $text1 = $form->addText('text1Name');
        $text2 = $form->addText('text2Name');

        $values = array(
            // for $text1
            'text1Name'             => 'text1Value',
            // should be ignored
            'unexistentElementName' => 'testValue',
        );

        $form->populate($values);

        // populate() only sets (protected) default value. The easiest way to check is to render it.
        $expectedText1 = '<p id="formName-text1Name" class="input-text" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="depage-label">text1Name</span>' .
                '<input name="text1Name" type="text" value="text1Value">' .
            '</label>' .
        '</p>' . "\n";
        $expectedText2 = '<p id="formName-text2Name" class="input-text" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="depage-label">text2Name</span>' .
                '<input name="text2Name" type="text" value="">' .
            '</label>' .
        '</p>' . "\n";

        $this->assertEquals($expectedText1, $text1->__toString());
        $this->assertEquals($expectedText2, $text2->__toString());
    }
    // }}}

    // {{{ testAddStep()
    /**
     * Adding step element.
     **/
    public function testAddStep()
    {
        $form = new htmlform('formName');
        $step = $form->addStep('stepName');

        $this->assertEquals('stepName', $step->getName());
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\step', $step);
    }
    // }}}

    // {{{ testGetCurrentElementsInStep()
    /**
     * Since getCurrentElements() is private we need to go through
     * inCurrentStep() and updateInputValue() in order to test it.
     *
     * This test is set up so that the update call happens when we're in the
     * right step (1). Hence, the element value should change to 'text0Value'.
     **/
    public function testGetCurrentElementsInStep()
    {
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
    // }}}

    // {{{ testGetCurrentElementsNotInStep()
    /**
     * Since getCurrentElements() is private we need to go through
     * inCurrentStep() and updateInputValue() in order to test it.
     *
     * This test is set up so that the update call happens when we're not in
     * the current step, so the initial value shouldn't change.
     **/
    public function testGetCurrentElementsNotInStep()
    {
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
    // }}}

    // {{{ testIsEmpty()
    /**
     * Tests if form has already been submitted
     **/
    public function testIsEmpty()
    {
        $form = new htmlform('formName');
        $this->assertTrue($form->isEmpty());

        $_SESSION['formName-data']['formName'] = 'formName';
        $this->assertFalse($form->isEmpty());
    }
    // }}}

    // {{{ testValidationResultWithoutValidate()
    /**
     * There's no need to rebuild the entire form with all its elements on the
     * success page in order to check the validation result. The form object
     * alone can check its validation result in the session data.
     **/
    public function testValidationResultWithoutValidate()
    {
        $form = new htmlform('formName');
        $this->assertNull($form->valid);

        $_SESSION['form2Name-data']['formIsValid'] = true;
        $form2 = new htmlform('form2Name');
        $this->assertTrue($form2->valid);

        $_SESSION['form3Name-data']['formIsValid'] = false;
        $form3 = new htmlform('form3Name');
        $this->assertFalse($form3->valid);
    }
    // }}}

    // {{{ testCustomValidator()
    /**
     * Forms can accept a custom validator function as a parameter. It's called
     * when the rest of the form is successfully validated and an array of form
     * element values is parsed to it.
     **/
    public function testCustomValidator()
    {
        //custom validator function (valid)
        $validator = function () { return true; };

        // pretend the form has been submitted before
        $_SESSION['formName-data']['formName'] = 'formName';

        // building the form with custom validator
        $form = new htmlform('formName', array('validator' => $validator));
        $form->validate();

        $this->assertTrue($form->valid);

        //custom validator function (invalid)
        $validator2 = function () { return false; };

        // pretending the form has been submitted before
        $_SESSION['form2Name-data']['form2Name'] = 'form2Name';

        // building the form with custom validator
        $form2 = new htmlform('form2Name', array('validator' => $validator2));
        $form2->validate();

        $this->assertFalse($form2->valid);
    }
    // }}}

    // {{{ testClearSession()
    /**
     * See if deleting the forms session slot works.
     **/
    public function testClearSession()
    {
        $_SESSION['formName-data']['formName'] = 'formName';
        $form = new htmlform('formName');

        $form->clearSession();

        $this->assertNull($form->getValues());
    }
    // }}}
}
