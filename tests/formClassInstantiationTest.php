<?php
require_once('../www/formClass.php');

class formClassInstantiationTest extends PHPUnit_Framework_TestCase {
    public function testFormNameNoStringException()
    {
        try {
            $form = new formClass(true, array());
        }
        catch (formNameNoStringException $expected) {
            return;
        }
        $this->fail('Expected formNameNoStringException.');
    }
    
    public function testInvalidFormNameException()
    {
        try {
            $form = new formClass(' ', array());
        }
        catch (invalidFormNameException $expected) {
            return;
        }
        $this->fail('Expected invalidFormNameException.');
    }
}
?>
