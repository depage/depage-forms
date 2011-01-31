<?php

require_once('../htmlform.php');

use depage\htmlform\htmlform;
use depage\htmlform\exceptions;

class htmlElementTest extends PHPUnit_Framework_TestCase {
    protected $form;

    protected function setUp() {
        $this->form = new htmlform('formNameString');
    }

    public function testHtmlClass() {
        $this->htmlElement = $this->form->addHtml('htmlString');
        $this->assertInstanceOf('\\depage\\htmlform\\elements\\html', $this->htmlElement);
    }

    public function testHtmlToString() {
        $this->htmlElement = $this->form->addHtml('htmlString');
        $this->assertEquals('htmlString', $this->htmlElement->__toString());
    }

    public function testHtmlNoStringException() {
        try {
            $this->htmlElement = $this->form->addHtml(55);
        } catch (exceptions\htmlNoStringException $expected) {
            return;
        }
    }
}
