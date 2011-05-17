<?php

use depage\htmlform\htmlform;

// {{{ htmlformTestClass
/**
 * Custom htmlform class with overidden redirect method for easier testing
 **/
class htmlformTestClass extends htmlform {
    public $testRedirect;

    public function redirect($url) {
        $this->testRedirect = $url;
    }
}
// }}}

/**
* Testing Post/Redirect/Get-relevant behavior
**/
class prgTest extends PHPUnit_Framework_TestCase {
    // {{{ testRedirect()
    /**
     * Testing the test...
     **/
    public function testRedirect() {
        $this->form = new htmlformTestClass('formName');
        $this->form->redirect('http://www.depage.net');

        $this->assertEquals('http://www.depage.net', $this->form->testRedirect);
    }
    // }}}

    // {{{ testProcessOnPost()
    /**
     * Testing htmlform::updateInputValue() and htmlform::process()
     * in case of submitted form
     **/
    public function testProcessOnPost() {
        // setting up the post-data (form-name and value for a text-element)
        $_POST['formName'] = 'formName';
        $_POST['postedText'] = 'submitted';

        $form = new htmlformTestClass('formName', array('successURL' => 'http://www.depagecms.net'));

        $postedTextElement = $form->addText('postedText');
        $unpostedTextElement = $form->addText('unpostedText');

        // tests post-data value
        $this->assertEquals($postedTextElement->getValue(), 'submitted');
        // tests value that hasn't been posted (set to null, then converted to empty string by setValue())
        $this->assertInternalType('string', $unpostedTextElement->getValue());
        $this->assertEquals('', $unpostedTextElement->getValue());

        $form->process();
        $this->assertTrue($form->validate());
        // should redirect to success address
        $this->assertEquals('http://www.depagecms.net', $form->testRedirect);
    }
    // }}}

    // {{{ testProcessOnGet()
    /**
     * Testing htmlform::updateInputValue() and htmlform::process()
     * on GET-request with previously submitted data in session
     **/
    public function testProcessOnGet() {
        // setting up session-data for text-element
        $_SESSION['formName-data']['storedText'] = 'stored';

        $form = new htmlformTestClass('formName');

        $storedTextElement = $form->addText('storedText');

        // tests value from session
        $this->assertEquals('stored', $storedTextElement->getValue());
    }
    // }}}

    // {{{ testProcessSteps()
    /**
     * Test process() method for forms with steps. Setting an invalid step 
     * number forces call of getFirstInvalidStep(). 
     *
     * The first invalid step should be step1.
     **/
    public function testProcessSteps() {
        $_POST['formName'] = 'formName';

        $_GET['step'] = 'bogusStepId';

        $form = new htmlformTestClass('formName');
        $step0 = $form->addStep('step0');
        $step1 = $form->addStep('step1');
        $step2 = $form->addStep('step2');
        $mail0 = $step0->addEmail('mail0');
        $mail0->setValue('valid@email.com');
        $mail1 = $step1->addEmail('mail1');
        $mail1->setValue('invalidEmail');
        $mail2 = $step2->addEmail('mail2');
        $mail2->setValue('invalidEmail');

        $form->process();

        $this->assertEquals('/?step=1', $form->testRedirect);
    }
    // }}}

    // {{{ testStepsFreeFieldset()
    /**
     * Tests getFirstInvalidStep() for fieldset outside steps. Setting an
     * invalid step number forces call of getFirstInvalidStep(). When all steps
     * are valid and the free fieldset is invalid it should jump to the last
     * step.
     **/
    public function testStepsFreeFieldset() {
        $_POST['formName'] = 'formName';

        $_GET['step'] = 'bogusStepId';

        $form = new htmlformTestClass('formName');
        $step0 = $form->addStep('step0');
        $step1 = $form->addStep('step1');
        $fieldset = $form->addFieldset('fieldset');
        $mail0 = $step0->addEmail('mail0');
        $mail0->setValue('valid@email.com');
        $mail1 = $step1->addEmail('mail1');
        $mail1->setValue('valid@email.com');
        $mail2 = $fieldset->addEmail('mail2');
        $mail2->setValue('invalidemail');

        $form->process();

        $this->assertEquals('/?step=1', $form->testRedirect);
    }
    // }}}
}
