<?php

use PHPUnit\Framework\TestCase;
use Depage\HtmlForm\Elements\Url;

/**
 * Test namespace handling
 **/
class NamespaceTest extends TestCase
{
    protected $form;

    // {{{ setUp()
    public function setUp(): void
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
     */
    public function testAddElementsFail()
    {
        $this->expectException(\Depage\HtmlForm\Exceptions\UnknownElementTypeException::class);
        $this->expectExceptionMessage("Unknown element type 'NamespaceTestClass");

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
    // {{{ testAddElementsCustomSubContainer
    /**
     * test if adding custom element with registered namespace works
     * for subcontainers
     */
    public function testAddElementsCustomSubContainer()
    {
        $this->form->registerNamespace('\\Depage\\HtmlForm\\Tests');
        $fieldset = $this->form->addFieldset('testFieldset');
        $text = $fieldset->addNamespaceTestClass('testText');
        $this->assertInstanceOf('\\Depage\\HtmlForm\\Tests\\NamespaceTestClass', $text);
    }
    // }}}
}
