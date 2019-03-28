<?php

use PHPUnit\Framework\TestCase;
use Depage\HtmlForm\Elements\Single;

/**
 * General tests for the multiple input element.
 **/
class singleTest extends TestCase
{
    // {{{ setUp()
    public function setUp():void
    {
        $this->form     = new nameTestForm;
        $this->single   = new Single('singleName', [
            'list' => [
                'valueString',
            ],
        ], $this->form);
    }
    // }}}

    // {{{ testConstruct()
    /**
     * Constructor test, getName()
     **/
    public function testConstruct()
    {
        $this->assertEquals('singleName', $this->single->getName());
    }
    // }}}

    // {{{ testSingleSetValue()
    /**
     * Tests setValue method with various values. (typecasting)
     **/
    public function testSingleSetValue()
    {
        $this->single->setValue('valueString');
        $this->assertEquals('valueString', $this->single->getValue());
    }
    // }}}

    // {{{ testSingleNotRequiredEmpty()
    /**
     * Not Required, empty -> valid
     **/
    public function testSingleNotRequiredEmpty()
    {
        $this->single->setValue('');
        $this->assertTrue($this->single->validate());
    }
    // }}}

    // {{{ testSingleValidNotRequiredNotEmpty()
    /**
     * Not empty, not required -> valid
     **/
    public function testSingleValidNotRequiredNotEmpty()
    {
        $this->single->setValue('valueString');
        $this->assertTrue($this->single->validate());
    }
    // }}}

    // {{{ testSingleRequiredEmpty()
    /**
     * Required, empty -> invalid
     **/
    public function testSingleRequiredEmpty()
    {
        $this->single->setRequired();
        $this->single->setValue('');
        $this->assertFalse($this->single->validate());
    }
    // }}}
}
