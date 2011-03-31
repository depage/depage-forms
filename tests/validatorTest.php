<?php

require_once('../validators/validator.php');

use depage\htmlform\validators\validator;

class validatorTest extends \PHPUnit_Framework_TestCase {
    public function testText() {
        $textValidator = validator::factory('text');
        $this->assertEquals(true, $textValidator->validate('anyString'));
    }

    public function testEmail() {
        $emailValidator = validator::factory('email');
        $this->assertEquals(false, $emailValidator->validate('anyString'));
        $this->assertEquals(true, $emailValidator->validate('test@depage.net'));
    }

   public function testUrl() {
        $urlValidator = validator::factory('url');
        $this->assertEquals(false, $urlValidator->validate('anyString'));
        $this->assertEquals(true, $urlValidator->validate('http://www.depage.net'));
    }

    public function testCustomRegEx() {
        $customValidator = validator::factory('/[a-zA-Z]/');
        $this->assertEquals(false, $customValidator->validate('1234'));
        $this->assertEquals(true, $customValidator->validate('letters'));
    }

    public function testNumber() {
        $numberValidator = validator::factory('number');
        $this->assertEquals(false, $numberValidator->validate('letters', null, null));
        $this->assertEquals(false, $numberValidator->validate(-10, 0, 10));
        $this->assertEquals(true, $numberValidator->validate(5, 0, 10));
        $this->assertEquals(true, $numberValidator->validate(5, null, null));
    }

    public function testGetPatternAttribute() {
        $regExValidator = validator::factory('/[a-z]/');
        $this->assertEquals(' pattern="[a-z]"', $regExValidator->getPatternAttribute());

        $telValidator = validator::factory('tel');
        $this->assertEquals('', $telValidator->getPatternAttribute());

        $anyValidator = validator::factory('foo');
        $this->assertEquals('', $anyValidator->getPatternAttribute());
    }
}
