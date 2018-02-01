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
}
