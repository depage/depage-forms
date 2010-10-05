<?php
require_once('../www/formClass.php');

class formClassTest extends PHPUnit_Framework_TestCase
{
    public function testFormNameNoStringException()
    {
        try {
            $form = new formClass(true , array());
        }
        catch (formNameNoStringException $expected) {
            unset($form);
            return;
        }
        $this->fail('Expected formNameNoStringException.');
    }
    
    public function testInvalidFormNameException()
    {
        try {
            $form = new formClass(' ' , array());
        }
        catch (invalidFormNameException $expected) {
            unset($form);
            return;
        }
        $this->fail('Expected invalidFormNameException.');
    }
    
    public function testDuplicateInputNameException()
    {
        try {
            
            $form = new formClass('duplicate' , array());
        }
        catch (invalidFormNameException $expected) {
            unset ($form);
            return;
        }
        $this->fail('Expected invalidFormNameException.');
    }
}
?>
