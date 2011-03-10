<?php

require_once('../htmlform.php');

use depage\htmlform\htmlform;

/**
 * Custom htmlform class with overidden redirect method for easier testing
 **/
class testHtmlform extends htmlform {
    public $testRedirect;

    public function redirect($url) {
        $this->testRedirect = $url;
    }
}

class prgTest extends PHPUnit_Framework_TestCase {
    /**
     * Testing the test...
     **/
    public function testRedirect() {
        $this->form = new testHtmlform('htmlformNameString');
        $this->form->redirect('http://www.depage.net');

        $this->assertEquals($this->form->testRedirect, 'http://www.depage.net');
    }

    /**
     * Testing htmlform::updateInputValue() and htmlform::process()
     * in case of submitted form
     **/
    public function testProcessOnPost() {
        // setting up the post-data (form-name and value for a text-element)
        $_POST['formName'] = 'htmlformNameString';
        $_POST['postedText'] = 'submitted';

        $form = new testHtmlform('htmlformNameString');

        $postedTextElement = $form->addText('postedText');
        $unpostedTextElement = $form->addText('unpostedText');

        // tests post-data value
        $this->assertEquals($postedTextElement->getValue(), 'submitted');
        // tests value that hasn't been posted (set to null, then converted to empty string by setValue())
        $this->assertInternalType('string', $unpostedTextElement->getValue());
        $this->assertEquals($unpostedTextElement->getValue(), '');

        $form->process();
        $this->assertTrue($form->validate());
        // should redirect to success address
        $this->assertEquals($form->testRedirect, $_SERVER['REQUEST_URI']);
    }

    /**
     * Testing htmlform::updateInputValue() and htmlform::process()
     * on GET-request with previously submitted data in session
     **/
    public function testProcessOnGet() {
        // setting up session-data for text-element
        $_SESSION['htmlformNameString-data']['storedText'] = 'stored';

        $form = new testHtmlform('htmlformNameString');

        $storedTextElement = $form->addText('storedText');

        // tests value from session
        $this->assertEquals($storedTextElement->getValue(), 'stored');
    }
}
