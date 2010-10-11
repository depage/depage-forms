<?php

class validatorTest extends PHPUnit_Framework_TestCase {
    public function testWildcard() {
        $wildcardValidator = new validator();
        $this->assertEquals(true, $wildcardValidator->match('anyString'));
    }

    public function testEmail() {
        $emailValidator = new validator('email');
        $this->assertEquals(false, $emailValidator->match('anyString'));
        $this->assertEquals(true, $emailValidator->match('test@testmail.com'));
    }

   public function testUrl() {
        $urlValidator = new validator('url');
        $this->assertEquals(false, $urlValidator->match('anyString'));
        $this->assertEquals(true, $urlValidator->match('http://www.testmail.com'));
    }

    public function testCustomRegEx() {
        $customValidator = new validator('/[a-zA-Z]/');
        $this->assertEquals(false, $customValidator->match('1234'));
        $this->assertEquals(true, $customValidator->match('letters'));
    }
}
