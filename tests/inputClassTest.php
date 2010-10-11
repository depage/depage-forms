<?php
require_once('../www/inputClass.php');
class inputClassTestClass extends inputClass {
    public function __construct($name, $parameters, $formName) {
        parent::__construct($name, $parameters, $formName);
    }
}

class inputClassTest extends PHPUnit_Framework_TestCase {
    public function testInputNameNoStringException() {
        try {
            $input = new inputClassTestClass(true, array(), 'formNameString');
        }
        catch (inputNameNoStringException $expected) {
            return;
        }
        $this->fail('Expected inputNameNoStringException.');
    }
    
    public function testInvalidInputNameException() {
        try {
            $form = new inputClassTestClass(' ', array(), 'formNameString');
        }
        catch (invalidInputNameException $expected) {
            return;
        }
        $this->fail('Expected invalidInputNameException.');
    }

    public function testInputParametersNoArrayException() {
        try {
            $form = new inputClassTestClass('inputNameString', 'string', 'formNameString');
        }
        catch (inputParametersNoArrayException $expected) {
            return;
        }
        $this->fail('Expected inputParametersNoArrayException.');
    }

    public function testInputClassValid() {
        $input = new inputClassTestClass('inputNameString', array(), 'formNameString');
        $this->assertEquals(true, $input->isValid());
        $input->validate();
        $this->assertEquals(true, $input->isValid());
        $input->setValue('testValue');
        $input->validate();
        $this->assertEquals(true, $input->isValid());
    }

    public function testGetName() {
        $input = new inputClassTestClass('inputNameString', array(), 'formNameString');
        $this->assertEquals('inputNameString', $input->getName());
    }
}
?>
