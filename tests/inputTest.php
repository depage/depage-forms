<?php

use depage\htmlform\abstracts\input;
use depage\htmlform\exceptions;

class inputTestClass extends input {
    public function __construct($name, $parameters, $form) {
        $parameters['validator'] = 'text';
        parent::__construct($name, $parameters, $form);
    }

    public function getHtmlClasses() {
        return $this->htmlClasses();
    }

    public function getHtmlErrorMessage() {
        return $this->htmlErrorMessage();
    }

    public function setValid($valid = true) {
        $this->valid = (bool) $valid;
    }

    // needed for testSetAutofocus
    public function getAutofocus() {
        return $this->autofocus;
    }

    // needed for testLog() (input::log() is protected)
    public function log($argument, $type) {
        parent::log($argument, $type);
    }
}

class inputTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->form     = new nameTestForm;
        $this->input    = new inputTestClass('inputName', array(), $this->form);
    }

    public function testInputNameNoStringException() {
        try {
            $input = new inputTestClass(true, array(), $this->form);
        }
        catch (exceptions\inputNameNoStringException $expected) {
            return;
        }
        $this->fail('Expected inputNameNoStringException.');
    }
    
    public function testInvalidInputNameException() {
        try {
            $input = new inputTestClass(' ', array(), $this->form);
        }
        catch (exceptions\invalidInputNameException $expected) {
            return;
        }
        $this->fail('Expected invalidInputNameException.');
    }

    public function testInputParametersNoArrayException() {
        try {
            $input = new inputTestClass('inputName', 'string', $this->form);
        }
        catch (exceptions\inputParametersNoArrayException $expected) {
            return;
        }
        $this->fail('Expected inputParametersNoArrayException.');
    }

    public function testInputInvalid() {
        $this->assertFalse($this->input->validate());
    }

    public function testInputValid() {
        $this->input->setValue('testValue');
        $this->assertTrue($this->input->validate());
    }

    public function testGetName() {
        $this->assertEquals('inputName', $this->input->getName());
    }

    public function testHtmlClasses() {
        $parameters = array('required' => true);
        $input = new inputTestClass('inputName', $parameters, $this->form);
        $input->setValue('');

        $this->assertEquals('input-inputtestclass required error', $input->getHtmlClasses());
    }

    public function testHtmlErrorMessage() {
        // valid input element => epmty error message
        $this->assertEquals($this->input->getHtmlErrorMessage(), '');

        // invalid input element
        $this->input->setValid(false);
        // set value (null by default)
        $this->input->setValue('');
        $this->assertEquals($this->input->getHtmlErrorMessage(), ' <span class="errorMessage">Please enter valid data!</span>');
    }

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

    public function testLog() {
        $log        = new logTestClass;

        $parameters = array('log' => $log);
        $input      = new inputTestClass('inputName', $parameters, $this->form);

        $input->log('argumentString', 'typeString');

        $expected = array(
            'argument'  => 'argumentString',
            'type'      => 'typeString',
        );

        $this->assertEquals($expected, $log->error);
    }
    public function undefinedMethodHandler($errno) {
            if ($errno = 256) throw new undefinedMethodException;
            return;
        }

    public function testUndefinedMethodError() {
        set_error_handler(array($this, 'undefinedMethodHandler'));
        try {
            $this->input->__call('undefined', 'argumentString');
        } catch (undefinedMethodException $expected) {
            restore_error_handler();
            return;
        }
        $this->fail('Expected undefinedMethodException');
    }
}
?>
