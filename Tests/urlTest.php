<?php

use PHPUnit\Framework\TestCase;
use Depage\HtmlForm\Elements\Url;

/**
 * General tests for the multiple input element.
 **/
class urlTest extends TestCase
{
    protected $form;
    protected $url;

    // {{{ setUp()
    public function setUp():void
    {
        $this->form = new nameTestForm;
        $this->url  = new Url('urlName', array(), $this->form);
    }
    // }}}

    // {{{ testConstruct()
    /**
     * Constructor test, getName()
     **/
    public function testConstruct()
    {
        $this->assertEquals('urlName', $this->url->getName());
    }
    // }}}

    // {{{ testSetValue()
    /**
     * Tests setValue method with various values. (typecasting)
     **/
    public function testSetValue()
    {
        $this->url->setValue('valueString');
        $this->assertEquals('valueString', $this->url->getValue());

        $this->url->setValue(42);
        $this->assertIsString($this->url->getValue());
        $this->assertEquals('42', $this->url->getValue());
    }
    // }}}

    // {{{ testNotRequiredEmpty()
    /**
     * Not required, empty -> valid
     **/
    public function testNotRequiredEmpty()
    {
        $this->url->setValue('');
        $this->assertTrue($this->url->validate());
    }
    // }}}

    // {{{ testValidNotRequiredNotEmpty()
    /**
     * Not required, not empty, valid value -> valid
     **/
    public function testValidNotRequiredNotEmpty()
    {
        $this->url->setValue('http://www.depage.com');
        $this->assertTrue($this->url->validate());
    }
    // }}}

    // {{{ testInvalidNotRequired()
    /**
     * Invalid value, not required -> invald
     **/
    public function testInvalidNotRequired()
    {
        $this->url->setValue('valueString');
        $this->assertFalse($this->url->validate());
    }
    // }}}

    // {{{ testRequiredEmpty()
    /**
     * Required, empty -> invalid
     **/
    public function testRequiredEmpty()
    {
        $this->url->setRequired();
        $this->url->setValue('');
        $this->assertFalse($this->url->validate());
    }
    // }}}
}
