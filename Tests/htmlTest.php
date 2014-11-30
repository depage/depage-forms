<?php

use Depage\HtmlForm\Elements\Html;

/**
 * Tests rendering custom HTML element.
 **/
class htmlTest extends PHPUnit_Framework_TestCase
{
    // {{{ testHtml()
    public function testHtml()
    {
        $html = new Html('htmlString');
        $this->assertEquals('htmlString', $html->__toString());
    }
    // }}}
}
