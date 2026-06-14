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

    // {{{ testUnsaitizedHtml()
    public function testUnsaitizedHtml()
    {
        $html = new Html('<div>htmlString</div>', ['sanitize' => false]);
        $this->assertEquals('<div>htmlString</div>', $html->__toString());
    }
    // }}}
}
