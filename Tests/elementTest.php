<?php

use PHPUnit\Framework\TestCase;
use Depage\HtmlForm\Abstracts\Element;
use Depage\HtmlForm\Exceptions;

// {{{ elementTestClass
/**
 * Element is abstract, so we need this test class to instantiate it.
 **/
class elementTestClass extends element
{
    // required for testSetParameters()
    protected function setDefaults()
    {
        parent::setDefaults();

        $this->defaults['testValue1'] = null;
        $this->defaults['TestValue2'] = null;
        $this->defaults['TESTValue3'] = null;
    }

    // needed for testLog() (element::log() is protected)
    public function log($argument, $type = null)
    {
        parent::log($argument, $type);
    }
}
// }}}

// {{{ undefinedMethodException
/**
 * Replacement exception for undefinedMethod Error
 **/
class undefinedMethodException extends Exception {}
// }}}

/**
 * General tests for the element class.
 **/
class elementTest extends TestCase
{
    // {{{ setUp()
    protected function setUp():void
    {
        $this->form = new nameTestForm;
        $this->element = new elementTestClass('elementName', array(), $this->form);
    }
    // }}}

    // {{{ testSetParameters()
    /**
     * Parameters can be set case insensitively but attributes are case
     * sensitive.
     **/
    public function testSetParameters()
    {
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
    public function testEmptyElementNameException()
    {
        $this->expectException(\Depage\HtmlForm\Exceptions\InvalidElementNameException::class);

        new elementTestClass(' ', array(), null);
    }
    // }}}

    // {{{ testElementNameNoStringException()
    /**
     * Throw an exception if name type isn't string.
     **/
    public function testElementNameNoStringException()
    {
        $this->expectException(\Depage\HtmlForm\Exceptions\InvalidElementNameException::class);

        new elementTestClass(42, array(), null);
    }
    // }}}

    // {{{ testInvalidElementNameException()
    /**
     * Throw an exception if name contains invalid characters.
     **/
    public function testInvalidElementNameException()
    {
        $this->expectException(\Depage\HtmlForm\Exceptions\InvalidElementNameException::class);

        new elementTestClass('/', array(), null);
    }
    // }}}

    // {{{ testElementParametersNoArrayException()
    /**
     * Element parameters need to be of type array.
     **/
    public function testElementParametersNoArrayException()
    {
        $this->expectException(\Depage\HtmlForm\Exceptions\ElementParametersNoArrayException::class);

        $input = new inputTestClass('inputName', 'string', $this->form);
    }
    // }}}

    // {{{ testLog()
    /**
     * Tests parsing a log object reference to the element. And calling it's log
     * method.
     **/
    public function testLog()
    {
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
}
