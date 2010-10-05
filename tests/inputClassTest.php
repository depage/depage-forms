<?php
require_once('../www/inputClass.php');
class inputClassTestClass extends inputClass {
    public function __construct($type, $name, $parameters, $formName) {
        parent::__construct($type, $name, $parameters, $formName);
    }
}

class inputClassTest extends PHPUnit_Framework_TestCase {
    public function testInputNameNoStringException()
    {
        try {
            $input = new inputClassTestClass('typeString', true, array(), 'formNameString');
        }
        catch (inputNameNoStringException $expected) {
            return;
        }
        $this->fail('Expected inputNameNoStringException.');
    }
    
    public function testInvalidInputNameException()
    {
        try {
            $form = new inputClassTestClass('typeString', ' ', array(), 'formNameString');
        }
        catch (invalidInputNameException $expected) {
            return;
        }
        $this->fail('Expected invalidInputNameException.');
    }
}
?>
