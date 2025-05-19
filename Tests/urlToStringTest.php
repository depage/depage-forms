<?php

use PHPUnit\Framework\TestCase;
use Depage\HtmlForm\Elements\Url;

/**
 * Tests for multiple input element rendering.
 **/
class urlToStringTest extends TestCase
{
    protected $form;
    protected $url;

    // {{{ setUp()
    public function setUp(): void
    {
        $this->form = new nameTestForm();
        $this->url = new Url('textName', [], $this->form);
    }
    // }}}

    // {{{ testValue()
    /**
     * Element with default setup and select skin.
     **/
    public function testValue()
    {
        $expected = '<p id="formName-textName" class="input-url" data-errorMessage="Please enter a valid URL">' .
            '<label>' .
                '<span class="depage-label">textName</span>' .
                '<input name="textName" type="url" value="http://depage.net">' .
            '</label>' .
        '</p>' . "\n";

        $this->url->setValue('http://depage.net');
        $this->assertEquals($expected, $this->url-> __toString());
    }
    // }}}

    // {{{ testUrlWithAllOptions()
    /**
     * Element with default setup and select skin.
     **/
    public function testUrlWithAllOptions()
    {
        $expected = '<p id="formName-textName" class="input-url" data-errorMessage="Please enter a valid URL">' .
            '<label>' .
                '<span class="depage-label">textName</span>' .
                '<input name="textName" type="url" value="https://username:password@depage.net:8443/path/to/file/?query=string#fragment">' .
            '</label>' .
        '</p>' . "\n";

        $this->url->setValue('https://username:password@depage.net:8443/path/to/file/?query=string#fragment');
        $this->assertEquals($expected, $this->url-> __toString());
    }
    // }}}

    // {{{ testIdnHostname()
    /**
     * Element with default setup and select skin.
     **/
    public function testIdnHostname()
    {
        $expected = '<p id="formName-textName" class="input-url" data-errorMessage="Please enter a valid URL">' .
            '<label>' .
                '<span class="depage-label">textName</span>' .
                '<input name="textName" type="url" value="https://täöüst.de">' .
            '</label>' .
        '</p>' . "\n";

        $this->url->setValue('https://täöüst.de');
        $this->assertEquals($expected, $this->url-> __toString());
    }
    // }}}

    // {{{ testUnencodedIdnHostname()
    /**
     * Element with default setup and select skin.
     **/
    public function testUnencodedIdnHostname()
    {
        $expected = '<p id="formName-textName" class="input-url" data-errorMessage="Please enter a valid URL">' .
            '<label>' .
                '<span class="depage-label">textName</span>' .
                '<input name="textName" type="url" value="https://täöüst.de">' .
            '</label>' .
        '</p>' . "\n";

        $this->url->setValue('https://xn--tst-qla4gpb.de');
        $this->assertEquals($expected, $this->url-> __toString());
    }
    // }}}
}
/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
