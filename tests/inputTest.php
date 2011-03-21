<?php
require_once('../abstracts/input.php');

use depage\htmlform\abstracts\input;
use depage\htmlform\exceptions;

class inputTestClass extends input {
    public function __construct($name, $parameters, $formName) {
        $parameters['validator'] = 'text';
        parent::__construct($name, $parameters, $formName);
    }
}

class inputTest extends PHPUnit_Framework_TestCase {
    public function testInputNameNoStringException() {
        try {
            $input = new inputTestClass(true, array(), 'formNameString');
        }
        catch (exceptions\inputNameNoStringException $expected) {
            return;
        }
        $this->fail('Expected inputNameNoStringException.');
    }
    
    public function testInvalidInputNameException() {
        try {
            $input = new inputTestClass(' ', array(), 'formNameString');
        }
        catch (exceptions\invalidInputNameException $expected) {
            return;
        }
        $this->fail('Expected invalidInputNameException.');
    }

    public function testInputParametersNoArrayException() {
        try {
            $input = new inputTestClass('inputNameString', 'string', 'formNameString');
        }
        catch (exceptions\inputParametersNoArrayException $expected) {
            return;
        }
        $this->fail('Expected inputParametersNoArrayException.');
    }

    public function testInputInalid() {
        $input = new inputTestClass('inputNameString', array(), 'formNameString');
        $this->assertEquals(false, $input->validate());
    }

    public function testInputValid() {
        $input = new inputTestClass('inputNameString', array(), 'formNameString');
        $input->setValue('testValue');
        $this->assertEquals(true, $input->validate());
    }

    public function testGetName() {
        $input = new inputTestClass('inputNameString', array(), 'formNameString');
        $this->assertEquals('inputNameString', $input->getName());
    }
}
?>
