<?php

use PHPUnit\Framework\TestCase;
use Depage\HtmlForm\Elements\Html;

/**
 * Tests rendering custom HTML element.
 **/
class htmlTest extends TestCase
{
    // {{{ testHtml()
    public function testHtml()
    {
        $html = new Html('htmlString');
        $this->assertEquals('htmlString', $html->__toString());
    }
    // }}}

    // {{{ testHtmlIsNotSanitizedByDefault()
    public function testHtmlIsNotSanitizedByDefault()
    {
        $html = new Html('<div id="myimage">htmlString</div>');
        $this->assertEquals('<div id="myimage">htmlString</div>', $html->__toString());
    }
    // }}}

    // {{{ testUnsaitizedHtml()
    public function testUnsaitizedHtml()
    {
        $html = new Html('<div>htmlString</div>', ['sanitize' => false]);
        $this->assertEquals('<div>htmlString</div>', $html->__toString());
    }
    // }}}

    // {{{ testSanitizedHtml()
    public function testSanitizedHtml()
    {
        $html = new Html('<div>htmlString</div>', ['sanitize' => true]);
        $this->assertEquals('<p>htmlString</p>', $html->__toString());
    }
    // }}}

    // {{{ testSanitizedHtmlWithDefaultAllowedTags()
    public function testSanitizedHtmlWithDefaultAllowedTags()
    {
        $html = new Html('<p>para <b>bold</b> <a href="https://example.com/">link</a></p>', ['sanitize' => true]);
        $this->assertEquals('<p>para <b>bold</b> <a href="https://example.com/">link</a></p>', $html->__toString());
    }
    // }}}

    // {{{ testSanitizedHtmlWithCustomAllowedTags()
    public function testSanitizedHtmlWithCustomAllowedTags()
    {
        $html = new Html('<div>htmlString</div>', ['sanitize' => true, 'allowedTags' => ['div']]);
        $this->assertEquals('<div>htmlString</div>', $html->__toString());
    }
    // }}}
}
