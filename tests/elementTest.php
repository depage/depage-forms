<?php

use depage\htmlform\abstracts\element;
use depage\htmlform\exceptions;

// {{{ elementTestClass
/**
 * Element is abstract, so we need this test class to instantiate it.
 **/
class elementTestClass extends element {
    // required for testSetParameters()
    protected function setDefaults() {
        parent::setDefaults();

        $this->defaults['testValue1'] = null;
        $this->defaults['TestValue2'] = null;
        $this->defaults['TESTValue3'] = null;
    }

    // needed for testLog() (element::log() is protected)
    public function log($argument, $type = null) {
        parent::log($argument, $type);
    }
}
// }}}

// {{{ undefinedMethodException
/**
 * Replacement exception for undefinedMethod Error
 **/
class undefinedMethodException extends exception {}
// }}}

/**
 * General tests for the element class.
 **/
class elementTest extends PHPUnit_Framework_TestCase {
    // {{{ setUp()
    protected function setUp() {
        $this->form = new nameTestForm;
        $this->element = new elementTestClass('elementName', array(), $this->form);
    }
    // }}}

    // {{{ testSetParameters()
    /**
     * Parameters can be set case insensitively but attributes are case
     * sensitive.
     **/
    public function testSetParameters() {
        $parameters = array(
            'testValue1' => '1',
            'testvalue2' => '2',
            'testvalue3' => '3',
        );

        $element = new elementTestClass('elementName', $parameters, null);

        $this->assertEquals('1', $element->testValue1);
        $this->assertEquals('2', $element->TestValue2);
        $this->assertEquals('3', $element->TESTValue3);
        $this->assertFalse(isset($element->testvalue3));
    }
    // }}}

    // {{{ testEmptyElementNameException()
    /**
     * Throw an exception on empty element name.
     **/
    public function testEmptyElementNameException() {
        try {
            new elementTestClass(' ', array(), null);
        } catch (exceptions\invalidElementNameException $expected) {
            return;
        }
        $this->fail('Expected invalidElementNameException.');
    }
    // }}}

    // {{{ testElementNameNoStringException()
    /**
     * Throw an exception if name type isn't string.
     **/
    public function testElementNameNoStringException() {
        try {
            new elementTestClass(42, array(), null);
        } catch (exceptions\invalidElementNameException $expected) {
            return;
        }
        $this->fail('Expected invalidElementNameException.');
    }
    // }}}

    // {{{ testInvalidElementNameException()
    /**
     * Throw an exception if name contains invalid characters.
     **/
    public function testInvalidElementNameException() {
        try {
            new elementTestClass('/', array(), null);
        } catch (exceptions\invalidElementNameException $expected) {
            return;
        }
        $this->fail('Expected invalidElementNameException.');
    }
    // }}}

    // {{{ testElementParametersNoArrayException()
    /**
     * Element parameters need to be of type array.
     **/
    public function testElementParametersNoArrayException() {
        try {
            $input = new inputTestClass('inputName', 'string', $this->form);
        }
        catch (exceptions\elementParametersNoArrayException $expected) {
            return;
        }
        $this->fail('Expected elementParametersNoArrayException.');
    }
    // }}}

    // {{{ testLog()
    /**
     * Tests parsing a log object reference to the element. And calling it's log
     * method.
     **/
    public function testLog() {
        $log        = new logTestClass;

        $parameters = array('log' => $log);
        $element       = new elementTestClass('elementName', $parameters, $this->form);

        $element->log('argumentString', 'typeString');

        $expected = array(
            'argument'  => 'argumentString',
            'type'      => 'typeString',
        );

        $this->assertEquals($expected, $log->error);
    }
    // }}}

    // {{{ undefinedMethodHandler()
    /**
     * required for testUndefinedMethodError
     **/
    public function undefinedMethodHandler($errno) {
        if ($errno = 256) throw new undefinedMethodException;
        return;
    }
    // }}}

    // {{{ testUndefinedMethodError()
    /**
     * If element::__call can't match a custom method, it triggers an
     * undefinedMethodError. We test this with our error handler by throwing
     * and catching an exception.
     **/
    public function testUndefinedMethodError() {
        set_error_handler(array($this, 'undefinedMethodHandler'));

        try {
            $this->element->__call('undefined', 'argumentString');
        } catch (undefinedMethodException $expected) {
            restore_error_handler();
            return;
        }
        $this->fail('Expected undefinedMethodException');
    }
    // }}}
}
