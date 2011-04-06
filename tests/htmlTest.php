<?php

use depage\htmlform\elements\html;

class htmlTest extends PHPUnit_Framework_TestCase {
    public function testHtml() {
        $html = new html('htmlString');
        $this->assertEquals('htmlString', $html->__toString());
    }
}
