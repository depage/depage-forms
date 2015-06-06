<?php

use Depage\HtmlForm\Elements\Url;

/**
 * Test namespace handling
 **/
class NamespaceTest extends PHPUnit_Framework_TestCase
{
    // {{{ setUp()
    public function setUp()
    {
        $this->form = new \Depage\HtmlForm\HtmlForm('testForm');
    }
    // }}}

    // {{{ testAddElementDefault
    /**
     * see if namespaces work for default element type
     **/
    public function testAddElementDefault()
    {
        $text = $this->form->addText('testText');

        $this->assertInstanceOf('\\Depage\\HtmlForm\\Elements\\Text', $text);
    }
    // }}}
    // {{{ testAddElementsFail
    /**
     * test if element from unregistered namespace fails
     *
     * @expectedException Depage\HtmlForm\Exceptions\UnknownElementTypeException
     * @expectedExceptionMessage Unknown element type 'NamespaceTestClass
     */
    public function testAddElementsFail()
    {
        $text = $this->form->addNamespaceTestClass('testText');
    }
    // }}}
    // {{{ testAddElementsCustom
    /**
     * test if adding custom element with registered namespace works
     */
    public function testAddElementsCustom()
    {
        $this->form->registerNamespace('\\Depage\\HtmlForm\\Tests');
        $text = $this->form->addNamespaceTestClass('testText');
        $this->assertInstanceOf('\\Depage\\HtmlForm\\Tests\\NamespaceTestClass', $text);
    }
    // }}}
}
