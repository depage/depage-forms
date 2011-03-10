<?php
require_once('../abstracts/inputClass.php');

use depage\htmlform\abstracts\inputClass;
use depage\htmlform\exceptions;

class inputClassTestClass extends inputClass {
    public function __construct($name, $parameters, $formName) {
        $parameters['validator'] = 'text';
        parent::__construct($name, $parameters, $formName);
    }
}

class inputClassTest extends PHPUnit_Framework_TestCase {
    public function testInputNameNoStringException() {
        try {
            $input = new inputClassTestClass(true, array(), 'formNameString');
        }
        catch (exceptions\inputNameNoStringException $expected) {
            return;
        }
        $this->fail('Expected inputNameNoStringException.');
    }
    
    public function testInvalidInputNameException() {
        try {
            $input = new inputClassTestClass(' ', array(), 'formNameString');
        }
        catch (exceptions\invalidInputNameException $expected) {
            return;
        }
        $this->fail('Expected invalidInputNameException.');
    }

    public function testInputParametersNoArrayException() {
        try {
            $input = new inputClassTestClass('inputNameString', 'string', 'formNameString');
        }
        catch (exceptions\inputParametersNoArrayException $expected) {
            return;
        }
        $this->fail('Expected inputParametersNoArrayException.');
    }

    public function testInputClassValid() {
        $input = new inputClassTestClass('inputNameString', array(), 'formNameString');
        $this->assertEquals(false, $input->validate());
    }

    public function testInputClassInvalid() {
        $input = new inputClassTestClass('inputNameString', array(), 'formNameString');
        $input->setValue('testValue');
        $this->assertEquals(true, $input->validate());
    }

    public function testGetName() {
        $input = new inputClassTestClass('inputNameString', array(), 'formNameString');
        $this->assertEquals('inputNameString', $input->getName());
    }
}
?>
