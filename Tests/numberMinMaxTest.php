<?php

use PHPUnit\Framework\TestCase;
use Depage\HtmlForm\Elements\Number;

/**
 * Tests for boolean input element rendering.
 **/
class numberMinMaxTest extends TestCase
{
    protected $form;

    // {{{ setUp()
    public function setUp():void
    {
        $this->form     = new nameTestForm;
    }
    // }}}

    // {{{ testEmpty()
    /**
     * Tests isEmpty method for various inputs
     **/
    public function testEmpty()
    {
        $number = new number('numberName', array(), $this->form);

        $number->setValue("");
        $this->assertTrue($number->isEmpty());

        $number->setValue("0");
        $this->assertFalse($number->isEmpty());

        $number->setValue("0.0");
        $this->assertFalse($number->isEmpty());

        $number->setValue("10");
        $this->assertFalse($number->isEmpty());
    }
    // }}}

    // {{{ testTypeCast()
    /**
     * Tests typcasting for various inputs including normalizing of decimal/thousand mark
     **/
    public function testTypeCast()
    {
        $number = new number('numberName', array(), $this->form);

        $number->setValue("");
        $this->assertNull($number->getValue());

        $number->setValue("a");
        $this->assertNull($number->getValue());

        $number->setValue("100a");
        $this->assertNull($number->getValue());

        $number->setValue("a100");
        $this->assertNull($number->getValue());

        $number->setValue("1-1");
        $this->assertNull($number->getValue());

        $number->setValue("0");
        $this->assertEquals(0, $number->getValue());

        $number->setValue("0.0");
        $this->assertEquals(0, $number->getValue());

        $number->setValue("-1");
        $this->assertEquals(-1, $number->getValue());

        $number->setValue("+1");
        $this->assertEquals(1, $number->getValue());

        $number->setValue("-.1");
        $this->assertEquals(-0.1, $number->getValue());

        $number->setValue(" 11 ");
        $this->assertEquals(11, $number->getValue());

        $number->setValue("10.15");
        $this->assertEquals(10.15, $number->getValue());

        $number->setValue("10,15");
        $this->assertEquals(10.15, $number->getValue());

        $number->setValue("1,000.15");
        $this->assertEquals(1000.15, $number->getValue());

        $number->setValue("1.000,15");
        $this->assertEquals(1000.15, $number->getValue());

        $number->setValue("1.000.000,15");
        $this->assertEquals(1000000.15, $number->getValue());

        $number->setValue("1,000,000.15");
        $this->assertEquals(1000000.15, $number->getValue());

        $number->setValue("1,000,000,015");
        $this->assertEquals(1000000015, $number->getValue());

        $number->setValue("1.000.000.015");
        $this->assertEquals(1000000015, $number->getValue());
    }
    // }}}

    // {{{ testMin()
    /**
     * Rendered element with set required attribute
     **/
    public function testMin()
    {
        $number = new number('numberName', array(
            "min" => 0,
            "required" => true,
        ), $this->form);

        $number->setValue("-1");
        $this->assertFalse($number->validate());

        $number->setValue("0");
        $this->assertTrue($number->validate());

        $number->setValue("1");
        $this->assertTrue($number->validate());
    }
    // }}}

    // {{{ testMax()
    /**
     * Rendered element with set required attribute
     **/
    public function testMax()
    {
        $number = new number('numberName', array(
            "max" => 0,
            "required" => true,
        ), $this->form);

        $number->setValue("-1");
        $this->assertTrue($number->validate());

        $number->setValue("0");
        $this->assertTrue($number->validate());

        $number->setValue("1");
        $this->assertFalse($number->validate());
    }
    // }}}
}
