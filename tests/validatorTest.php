<?php

use depage\htmlform\validators\validator;
use depage\htmlform\validators\regEx;

// {{{ validatorTestClass
/**
 * Dummy validator class
 **/
class validatorTestClass extends validator {
    // needed for testLog() ($this->log() is protected)
    public function log($argument, $type = null) {
        parent::log ($argument, $type);
    }
}
// }}}

// {{{ regExTestClass
/**
 * Dummy regEx validator class
 **/
class regExTestClass extends regEx {
    public function __construct($regEx, $log) {
        parent::__construct($log);
        $this->regEx = $regEx;
    }
    // needed for testLog() ($this->log() is protected)
    public function log($argument, $type = null) {
        parent::log ($argument, $type);
    }
}
// }}}

/**
 * General tests for the validator class.
 **/
class validatorTest extends \PHPUnit_Framework_TestCase {
    // {{{ testText()
    /**
     * Text validator always returns true
     **/
    public function testText() {
        $textValidator = validator::factory('text');
        $this->assertTrue($textValidator->validate('anyString'));
    }
    // }}}

    // {{{ testEmail()
    /**
     * Email validator test
     **/
    public function testEmail() {
        $emailValidator = validator::factory('email');
        $this->assertFalse($emailValidator->validate('anyString'));
        $this->assertTrue($emailValidator->validate('test@depage.net'));
    }
    // }}}

    // {{{ testUrl()
    /**
     * Url validator test
     **/
    public function testUrl() {
        $urlValidator = validator::factory('url');
        $this->assertFalse($urlValidator->validate('anyString'));
        $this->assertTrue($urlValidator->validate('http://www.depage.net'));
    }
    // }}}

    // {{{ testTel()
    /**
     * Tel validator test
     **/
    public function testTel() {
        $urlValidator = validator::factory('tel');
        $this->assertFalse($urlValidator->validate('anyString'));
        $this->assertTrue($urlValidator->validate('+(123)-32 2.3'));
    }
    // }}}

    // {{{ testCustomRegEx()
    /**
     * Creating a custom validator by parsing a regular expression
     **/
    public function testCustomRegEx() {
        $customValidator = validator::factory('/[a-zA-Z]/');
        $this->assertFalse($customValidator->validate('1234'));
        $this->assertFalse($customValidator->validate('letters'));
        $this->assertTrue($customValidator->validate('l'));
    }
    // }}}

    // {{{ testNumber()
    /**
     * Number validator test; using limits set in parameter array
     **/
    public function testNumber() {
        $numberValidator = validator::factory('number');
        $this->assertFalse($numberValidator->validate('letters',   array('min' => null,    'max' => null)));
        $this->assertFalse($numberValidator->validate(-10,         array('min' => 0,       'max' => 10)));
        $this->assertTrue($numberValidator->validate(5,           array('min' => 0,       'max' => 10)));
        $this->assertTrue($numberValidator->validate(5,           array('min' => null,    'max' => null)));
    }
    // }}}

    // {{{ testClosure()
    /**
     * Creating a custom closure validator
     **/
    public function testClosure() {
        $testClosure = function($value, $parameters) {
            return $value == "is-true";
        };
        $customValidator = validator::factory($testClosure);
        $this->assertFalse($customValidator->validate('1234'));
        $this->assertTrue($customValidator->validate('is-true'));
    }
    // }}}

    // {{{ testGetPatternAttribute()
    /**
     * Getting HTML5 pattern attribute for various validator types
     **/
    public function testGetPatternAttribute() {
        // custom validator returns formatted regular expression
        $regExValidator = validator::factory('/[a-z]/');
        $this->assertEquals(' pattern="[a-z]"', $regExValidator->getPatternAttribute());

        // telephone number validator returns empty string (tel inputs are automatically validated)
        $telValidator = validator::factory('tel');
        $this->assertInternalType('string', $telValidator->getPatternAttribute());
        $this->assertEquals('', $telValidator->getPatternAttribute());

        // unknown validators are turned into text validators -> they accept anything -> empty string
        $anyValidator = validator::factory('foo');
        $this->assertInternalType('string', $telValidator->getPatternAttribute());
        $this->assertEquals('', $anyValidator->getPatternAttribute());
    }
    // }}}

    // {{{ testLog()
    /**
     * Testing the internal log method
     **/
    public function testLog() {
        $log        = new logTestClass;
        $validator  = new validatorTestClass($log);

        $validator->log('argumentString', 'typeString');

        $expected = array(
            'argument'  => 'argumentString',
            'type'      => 'typeString',
        );

        $this->assertEquals($expected, $log->error);
    }
    // }}}

    // {{{ testPregError()
    /**
     * Testing error logging on preg_match error.
     * Example error from http://php.net/manual/en/function.preg-last-error.php
     **/
    public function testPregError() {
        $log        = new logTestClass;
        $regEx      = '/(?:\D+|<\d+>)*[!?]/';
        $validator  = new regExTestClass($regEx, $log);

        $validator->validate('foobar foobar foobar');

        $expected = array(
            'argument'  => 'Regular expression warning: error code 2',
            'type'      => null,
        );

        $this->assertEquals($expected, $log->error);
    }
    // }}}
}
