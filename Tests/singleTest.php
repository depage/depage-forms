<?php

use PHPUnit\Framework\TestCase;
use Depage\HtmlForm\Elements\Single;

/**
 * General tests for the multiple input element.
 **/
class singleTest extends TestCase
{
    protected $form;
    protected $single;
    protected $singleAssoc;

    // {{{ setUp()
    public function setUp():void
    {
        $this->form     = new nameTestForm;
        $this->single   = new Single('singleName', [
            'list' => [
                'valueString',
                'valueString2',
            ],
        ], $this->form);
        $this->singleAssoc = new Single('singleName', [
            'list' => [
                'valueString' => "label 1",
                'valueString2' => "label 2",
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
        $this->single->setValue('0');
        $this->assertEquals('0', $this->single->getValue());

        $this->single->setValue('1');
        $this->assertEquals('1', $this->single->getValue());
    }
    // }}}

    // {{{ testSingleSetValueNotAvailable()
    /**
     * Tests setValue method with various values. (typecasting)
     **/
    public function testSingleSetValueNotAvailable()
    {
        $this->single->setValue('2');
        $this->assertEquals('', $this->single->getValue());
    }
    // }}}

    // {{{ testSingleSetValueAssoc()
    /**
     * Tests setValue method with various values. (typecasting)
     **/
    public function testSingleSetValueAssoc()
    {
        $this->singleAssoc->setValue('valueString');
        $this->assertEquals('valueString', $this->singleAssoc->getValue());

        $this->singleAssoc->setValue('valueString2');
        $this->assertEquals('valueString2', $this->singleAssoc->getValue());
    }
    // }}}

    // {{{ testSingleSetValueNotAvailableAssoc()
    /**
     * Tests setValue method with various values. (typecasting)
     **/
    public function testSingleSetValueNotAvailableAssoc()
    {
        $this->singleAssoc->setValue('valueStringNotAvailable');
        $this->assertEquals('', $this->singleAssoc->getValue());
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
