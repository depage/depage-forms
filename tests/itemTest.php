<?php

use depage\htmlform\abstracts\item;
use depage\htmlform\exceptions;

class itemTestClass extends item {
    protected function setDefaults() {
        parent::setDefaults();

        $this->defaults['testValue1'] = null;
        $this->defaults['TestValue2'] = null;
        $this->defaults['TESTValue3'] = null;
    }

    // needed for testLog() (item::log() is protected)
    public function log($argument, $type) {
        parent::log($argument, $type);
    }
}

class itemTest extends PHPUnit_Framework_TestCase {
    protected function setUp() {
        $this->form = new nameTestForm;
        $this->item = new itemTestClass('itemName', array(), $this->form);
    }

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

        $item = new itemTestClass('itemName', $parameters, null);

        $this->assertEquals('1', $item->testValue1);
        $this->assertEquals('2', $item->TestValue2);
        $this->assertEquals('3', $item->TESTValue3);
        $this->assertNull($item->testvalue3);
    }
    
    public function testInvalidItemNameException() {
        try {
            new itemTestClass(' ', array(), null);
        } catch (exceptions\invalidItemNameException $expected) {
            return;
        }
        $this->fail('Expected invalidItemNameException.');
    }

    public function testItemNameNoStringException() {
        try {
            new itemTestClass(42, array(), null);
        } catch (exceptions\itemNameNoStringException $expected) {
            return;
        }
        $this->fail('Expected itemNameNoStringException.');
    }

    public function testItemParametersNoArrayException() {
        try {
            $input = new inputTestClass('inputName', 'string', $this->form);
        }
        catch (exceptions\itemParametersNoArrayException $expected) {
            return;
        }
        $this->fail('Expected itemParametersNoArrayException.');
    }

    public function testLog() {
        $log        = new logTestClass;

        $parameters = array('log' => $log);
        $item       = new itemTestClass('itemName', $parameters, $this->form);

        $item->log('argumentString', 'typeString');

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
            $this->item->__call('undefined', 'argumentString');
        } catch (undefinedMethodException $expected) {
            restore_error_handler();
            return;
        }
        $this->fail('Expected undefinedMethodException');
    }
}
