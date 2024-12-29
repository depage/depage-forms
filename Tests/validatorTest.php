<?php

use PHPUnit\Framework\TestCase;
use Depage\HtmlForm\Validators\Validator;
use Depage\HtmlForm\Validators\RegEx;

// {{{ validatorTestClass
/**
 * Dummy validator class
 **/
class validatorTestClass extends Validator
{
    // needed for testLog() ($this->log() is protected)
    public function log($argument, $type = null)
    {
        parent::log($argument, $type);
    }
}
// }}}

// {{{ regExTestClass
/**
 * Dummy regEx validator class
 **/
class regExTestClass extends RegEx
{
    public function __construct($regEx, $log)
    {
        parent::__construct($log);
        $this->regEx = $regEx;
    }
    // needed for testLog() ($this->log() is protected)
    public function log($argument, $type = null)
    {
        parent::log($argument, $type);
    }
}
// }}}

/**
 * General tests for the validator class.
 **/
class validatorTest extends TestCase
{
    // {{{ testText()
    /**
     * Text validator always returns true
     **/
    public function testText()
    {
        $textValidator = Validator::factory('Text');
        $this->assertTrue($textValidator->validate('anyString'));
    }
    // }}}

    // {{{ testEmail()
    /**
     * Email validator test
     **/
    public function testEmail()
    {
        $emailValidator = Validator::factory('Email');
        $this->assertFalse($emailValidator->validate('anyString', ['checkDns' => false]));
        $this->assertTrue($emailValidator->validate('test@depage.net', ['checkDns' => false]));
    }
    // }}}

    // {{{ testUrl()
    /**
     * Url validator test
     **/
    public function testUrl()
    {
        $urlValidator = Validator::factory('Url');
        $this->assertFalse($urlValidator->validate('anyString'));
        $this->assertTrue($urlValidator->validate('http://www.depage.net'));
    }
    // }}}

    // {{{ testTel()
    /**
     * Tel validator test
     **/
    public function testTel()
    {
        $urlValidator = Validator::factory('Tel');
        $this->assertFalse($urlValidator->validate('anyString'));
        $this->assertTrue($urlValidator->validate('+(123)-32 2.3'));
    }
    // }}}

    // {{{ testCustomRegEx()
    /**
     * Creating a custom validator by parsing a regular expression
     **/
    public function testCustomRegEx()
    {
        $customValidator = Validator::factory('/[a-zA-Z]/');
        $this->assertFalse($customValidator->validate('1234'));
        $this->assertFalse($customValidator->validate('letters'));
        $this->assertTrue($customValidator->validate('l'));
    }
    // }}}

    // {{{ testNumber()
    /**
     * Number validator test; using limits set in parameter array
     **/
    public function testNumber()
    {
        $numberValidator = Validator::factory('Number');
        $this->assertFalse($numberValidator->validate('letters', ['min' => null,    'max' => null]));
        $this->assertFalse($numberValidator->validate(-10, ['min' => 0,       'max' => 10]));
        $this->assertTrue($numberValidator->validate(5, ['min' => 0,       'max' => 10]));
        $this->assertTrue($numberValidator->validate(5, ['min' => null,    'max' => null]));
    }
    // }}}

    // {{{ testClosure()
    /**
     * Creating a custom closure validator
     **/
    public function testClosure()
    {
        $testClosure = function ($value, $parameters) {
            return $value == "is-true";
        };
        $customValidator = Validator::factory($testClosure);
        $this->assertFalse($customValidator->validate('1234'));
        $this->assertTrue($customValidator->validate('is-true'));
    }
    // }}}

    // {{{ testGetPatternAttribute()
    /**
     * Getting HTML5 pattern attribute for various validator types
     **/
    public function testGetPatternAttribute()
    {
        // custom validator returns formatted regular expression
        $regExValidator = Validator::factory('/[a-z]/');
        $this->assertEquals(' pattern="[a-z]"', $regExValidator->getPatternAttribute());

        // telephone number validator returns empty string (tel inputs are automatically validated)
        $telValidator = Validator::factory('Tel');
        $this->assertIsString($telValidator->getPatternAttribute());
        $this->assertEquals('', $telValidator->getPatternAttribute());

        // unknown validators are turned into text validators -> they accept anything -> empty string
        $anyValidator = Validator::factory('foo');
        $this->assertIsString($telValidator->getPatternAttribute());
        $this->assertEquals('', $anyValidator->getPatternAttribute());
    }
    // }}}

    // {{{ testLog()
    /**
     * Testing the internal log method
     **/
    public function testLog()
    {
        $log        = new logTestClass();
        $validator  = new validatorTestClass($log);

        $validator->log('argumentString', 'typeString');

        $expected = [
            'argument'  => 'argumentString',
            'type'      => 'typeString',
        ];

        $this->assertEquals($expected, $log->error);
    }
    // }}}

    // {{{ testPregError()
    /**
     * Testing error logging on preg_match error.
     * Example error from http://php.net/manual/en/function.preg-last-error.php
     **/
    public function testPregError()
    {
        $log        = new logTestClass();
        $regEx      = '/(?:\D+|<\d+>)*[!?]/';
        $validator  = new regExTestClass($regEx, $log);

        $validator->validate('foobar foobar foobar');

        $expected = [
            'argument'  => 'Regular expression warning: error code 2',
            'type'      => null,
        ];

        $this->assertEquals($expected, $log->error);
    }
    // }}}
}
