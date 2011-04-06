<?php

use depage\htmlform\abstracts\input;
use depage\htmlform\exceptions;

class inputTestClass extends input {
    public function __construct($name, $parameters, $formName) {
        $parameters['validator'] = 'text';
        parent::__construct($name, $parameters, $formName);
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
    public function testInputNameNoStringException() {
        try {
            $input = new inputTestClass(true, array(), 'formName');
        }
        catch (exceptions\inputNameNoStringException $expected) {
            return;
        }
        $this->fail('Expected inputNameNoStringException.');
    }
    
    public function testInvalidInputNameException() {
        try {
            $input = new inputTestClass(' ', array(), 'formName');
        }
        catch (exceptions\invalidInputNameException $expected) {
            return;
        }
        $this->fail('Expected invalidInputNameException.');
    }

    public function testInputParametersNoArrayException() {
        try {
            $input = new inputTestClass('inputName', 'string', 'formName');
        }
        catch (exceptions\inputParametersNoArrayException $expected) {
            return;
        }
        $this->fail('Expected inputParametersNoArrayException.');
    }

    public function testInputInvalid() {
        $input = new inputTestClass('inputName', array(), 'formName');
        $this->assertEquals(false, $input->validate());
    }

    public function testInputValid() {
        $input = new inputTestClass('inputName', array(), 'formName');
        $input->setValue('testValue');
        $this->assertEquals(true, $input->validate());
    }

    public function testGetName() {
        $input = new inputTestClass('inputName', array(), 'formName');
        $this->assertEquals('inputName', $input->getName());
    }

    public function testHtmlClasses() {
        $input = new inputTestClass('inputName', array('required' => true), 'formName');
        $input->setValue('');

        $this->assertEquals($input->getHtmlClasses(), 'input-inputtestclass required error');
    }

    public function testHtmlErrorMessage() {
        $input = new inputTestClass('inputName', array(), 'formName');

        // valid input element => epmty error message
        $this->assertEquals($input->getHtmlErrorMessage(), '');

        // invalid input element
        $input->setValid(false);
        // set value (null by default)
        $input->setValue('');
        $this->assertEquals($input->getHtmlErrorMessage(), ' <span class="errorMessage">Please enter valid data!</span>');
    }

    public function testSetAutofocus() {
        $input = new inputTestClass('inputName', array(), 'formName');

        // initially autofocus is set to false
        $this->assertFalse($input->getAutofocus());

        // no parameter means true
        $input->setAutofocus();
        $this->assertTrue($input->getAutofocus());

        $input->setAutofocus(false);
        $this->assertFalse($input->getAutofocus());

        $input->setAutofocus(true);
        $this->assertTrue($input->getAutofocus());
    }

    public function testLog() {
        $log        = new logTestClass;

        $parameters = array('log' => $log);
        $input      = new inputTestClass('inputName', $parameters, 'formName');

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
        $parameters = array();
        $input = new inputTestClass('inputName', $parameters, 'formName');

        set_error_handler(array($this, 'undefinedMethodHandler'));
        try {
            $input->__call('undefined', 'argumentString');
        } catch (undefinedMethodException $expected) {
            restore_error_handler();
            return;
        }
        $this->fail('Expected undefinedMethodException');
    }
}
?>
