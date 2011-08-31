<?php

use depage\htmlform\abstracts\input;

// {{{ inputTestClass
/**
 * Input is abstract, so we need this test class to instantiate it.
 **/
class inputTestClass extends input {
    // parent is protected
    public function htmlClasses() {
        return parent::htmlClasses();
    }

    // parent is protected
    public function htmlErrorMessage() {
        return parent::htmlErrorMessage();
    }

    public function setValid($valid = true) {
        $this->valid = (bool) $valid;
    }

    // needed for testSetAutofocus
    public function getAutofocus() {
        return $this->autofocus;
    }
}
// }}}

/**
 * General tests for the input class.
 **/
class inputTest extends PHPUnit_Framework_TestCase {
    // {{{ setUp()
    public function setUp() {
        $this->form     = new nameTestForm;
        $this->input    = new inputTestClass('inputName', array(), $this->form);
    }
    // }}}

    // {{{ testInputInvalid()
    /**
     * Default value is null -> invalid
     **/
    public function testInputInvalid() {
        $this->assertFalse($this->input->validate());
    }
    // }}}

    // {{{ testInputValid()
    /**
     * After setting value -> valid
     **/
    public function testInputValid() {
        $this->input->setValue('testValue');
        $this->assertTrue($this->input->validate());
    }
    // }}}

    // {{{ testGetName()
    /**
     * Testing getName method
     **/
    public function testGetName() {
        $this->assertEquals('inputName', $this->input->getName());
    }
    // }}}

    // {{{ testHtmlClasses()
    /**
     * Tests getting rendered HTML classes.
     **/
    public function testHtmlClasses() {
        $input = new inputTestClass('inputName', array(), $this->form);

        // default
        $this->assertEquals('input-inputtestclass', $input->htmlClasses());

        // required
        $input->setRequired();
        $this->assertEquals('input-inputtestclass required', $input->htmlClasses());
        $input->setRequired(false);

        // disabled
        $input->setDisabled();
        $this->assertEquals('input-inputtestclass disabled', $input->htmlClasses());
        $input->setDisabled(false);

        // required & error
        $input->setRequired();
        $input->setValue('');
        $input->validate();
        $this->assertEquals('input-inputtestclass required error', $input->htmlClasses());
    }
    // }}}

    // {{{ testHtmlErrorMessage()
    /**
     * Tests getting rendered HTML error message.
     **/
    public function testHtmlErrorMessage() {
        // valid input element => epmty error message
        $this->assertEquals($this->input->htmlErrorMessage(), '');

        // invalid input element
        $this->input->setValid(false);
        // set value (null by default)
        $this->input->setValue('');
        $this->assertEquals($this->input->htmlErrorMessage(), '<span class="errorMessage">Please enter valid data</span>');
    }
    // }}}

    // {{{ testSetAutofocus()
    /**
     * Tests setAutofocus method
     **/
    public function testSetAutofocus() {
        // initially autofocus is set to false
        $this->assertFalse($this->input->getAutofocus());

        // no parameter means true
        $this->input->setAutofocus();
        $this->assertTrue($this->input->getAutofocus());

        $this->input->setAutofocus(false);
        $this->assertFalse($this->input->getAutofocus());

        $this->input->setAutofocus(true);
        $this->assertTrue($this->input->getAutofocus());
    }
    // }}}
}
