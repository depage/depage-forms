<?php

use depage\htmlform\elements\html;

/**
 * Tests rendering custom HTML element.
 **/
class htmlTest extends PHPUnit_Framework_TestCase {
    // {{{ testHtml()
    public function testHtml() {
        $html = new html('htmlString');
        $this->assertEquals('htmlString', $html->__toString());
    }
    // }}}
}
