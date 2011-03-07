<?php

namespace depage\htmlform\validators;

require_once('../validators/validator.php');

class validatorTest extends \PHPUnit_Framework_TestCase {
    public function testText() {
        $textValidator = validator::factory('text');
        $this->assertEquals(true, $textValidator->validate('anyString'));
    }

    public function testEmail() {
        $emailValidator = validator::factory('email');
        $this->assertEquals(false, $emailValidator->validate('anyString'));
        $this->assertEquals(true, $emailValidator->validate('test@testmail.com'));
    }

   public function testUrl() {
        $urlValidator = validator::factory('url');
        $this->assertEquals(false, $urlValidator->validate('anyString'));
        $this->assertEquals(true, $urlValidator->validate('http://www.testmail.com'));
    }

    public function testCustomRegEx() {
        $customValidator = validator::factory('/[a-zA-Z]/');
        $this->assertEquals(false, $customValidator->validate('1234'));
        $this->assertEquals(true, $customValidator->validate('letters'));
    }
}
