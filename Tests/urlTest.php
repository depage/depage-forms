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
    public function setUp(): void
    {
        $this->form = new nameTestForm();
        $this->url  = new Url('urlName', [], $this->form);
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

    // {{{ testSetIdnUrl()
    /**
     * Tests setValue with an IDN url
     **/
    public function testSetIdnUrl()
    {
        $this->url->setValue("https://äöüß-test.de");
        $this->assertEquals("https://xn--ss-test-4wa6n9b.de", $this->url->getValue());
        $this->assertTrue($this->url->validate());
    }
    // }}}

    // {{{ testSetUrlWithAllOptions()
    /**
     * Tests setValue with an IDN url
     **/
    public function testSetUrlWithAllOptions()
    {
        $this->url->setValue("https://username:password@depage.net:8443/path/to/file/?query=string#fragment");
        $this->assertEquals("https://username:password@depage.net:8443/path/to/file/?query=string#fragment", $this->url->getValue());
        $this->assertTrue($this->url->validate());
    }
    // }}}

    // {{{ testSetPathUrlEncoding()
    /**
     * Tests setValue with an URL containing special characters
     **/
    public function testSetPathUrlEncoding()
    {
        $this->url->setValue("https://depage.net/äöüß-test/");
        $this->assertEquals("https://depage.net/%C3%A4%C3%B6%C3%BC%C3%9F-test/", $this->url->getValue());
        $this->assertTrue($this->url->validate());
    }
    // }}}

    // {{{ testDisabledNormalization()
    /**
     * Tests setValue with an URL containing special characters
     **/
    public function testDisabledNormalization()
    {
        $url  = new Url('urlName2', [
            'normalize' => false,
        ], $this->form);

        $url->setValue("https://äöüß-test.de/äöüß-test/");
        $this->assertEquals("https://äöüß-test.de/äöüß-test/", $url->getValue());
        $this->assertTrue($url->validate());
    }
    // }}}

    // {{{ testSetPathUrlEncodingNoDoubleEncode()
    /**
     * Tests setValue to make sure that the URL is not double encoded
     **/
    public function testSetPathUrlEncodingNoDoubleEncode()
    {
        $this->url->setValue("https://depage.net/%C3%A4%C3%B6%C3%BC%C3%9F-test/");
        $this->assertEquals("https://depage.net/%C3%A4%C3%B6%C3%BC%C3%9F-test/", $this->url->getValue());
        $this->assertTrue($this->url->validate());
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
        $this->url->setValue('http://depage.net');
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
