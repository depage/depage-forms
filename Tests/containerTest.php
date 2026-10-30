<?php

use PHPUnit\Framework\TestCase;
use Depage\HtmlForm\Abstracts\Container;
use Depage\HtmlForm\Exceptions;

// {{{ containerTestClass
/**
 * Container is abstract, so we need this test class to instantiate it.
 **/
class containerTestClass extends Container
{
    // needed for testSetRequired()
    public function addTestElement($element)
    {
        $this->elements[] = $element;
    }
}
// }}}

// {{{ testElement
/**
 * needed for testSetRequired()
 **/
class testElement
{
    public $required = false;

    public function setRequired($required)
    {
        $this->required = $required;
    }

    // required for getElement test
    public function getName()
    {
        return 'testElementName';
    }
}
// }}}

/**
 * General tests for the container class.
 **/
class containerTest extends TestCase
{
    protected $form;
    protected $container;

    // {{{ setUp()
    protected function setUp(): void
    {
        $this->form         = new nameTestForm();
        $this->container    = new containerTestClass('containerName', [], $this->form);
    }
    // }}}

    // {{{ testConstruct()
    /**
     * Constructor test.
     **/
    public function testConstruct()
    {
        $this->assertEquals('containerName', $this->container->getName());
    }
    // }}}

    // {{{ testGetElements()
    /**
     * Tests method to get subelements of container.
     **/
    public function testGetElements()
    {
        $text1      = $this->container->addText('text1');
        $fieldset   = $this->container->addFieldset('fieldset');
        $text2      = $fieldset->addText('text2');
        $text3      = $this->container->addText('text3');

        // input elements only
        $elements = $this->container->getElements();
        $this->assertEquals($text1, $elements[0]);
        $this->assertEquals($text2, $elements[1]);
        $this->assertEquals($text3, $elements[2]);

        // elements including other container elements
        $elements = $this->container->getElements(true);
        $this->assertEquals($text1, $elements[0]);
        $this->assertEquals($fieldset, $elements[1]);
        $this->assertEquals($text2, $elements[2]);
        $this->assertEquals($text3, $elements[3]);
    }
    // }}}

    // {{{ testcheckElementType()
    /**
     * Exception on unknown element type.
     **/
    public function testcheckElementType()
    {
        $this->expectException(\Depage\HtmlForm\Exceptions\UnknownElementTypeException::class);

        $this->container->addElement('bogusType', 'elementName');
    }
    // }}}

    // {{{ testAddHtml()
    /**
     * Tests addHtml method.
     **/
    public function testAddHtml()
    {
        $html = $this->container->addHtml('htmlString');
        $this->assertEquals('htmlString', $html->__toString());
    }
    // }}}

    // {{{ testAddHtmlIsNotSanitizedByDefault()
    /**
     * Tests that addHtml prints the HTML as-is by default.
     **/
    public function testAddHtmlIsNotSanitizedByDefault()
    {
        $html = $this->container->addHtml('<div id="myimage">htmlString</div>');
        $this->assertEquals('<div id="myimage">htmlString</div>', $html->__toString());
    }
    // }}}

    // {{{ testAddHtmlWithSanitizeParameter()
    /**
     * Tests addHtml method with sanitize-parameter.
     **/
    public function testAddHtmlWithSanitizeParameter()
    {
        $html = $this->container->addHtml('<div>htmlString</div>', ['sanitize' => true]);
        $this->assertEquals('<p>htmlString</p>', $html->__toString());
    }
    // }}}

    // {{{ testAddSanitizedHtml()
    /**
     * Tests addSanitizedHtml method with default allowed tags.
     **/
    public function testAddSanitizedHtml()
    {
        // not allowed tags get removed
        $html = $this->container->addSanitizedHtml('<div>htmlString</div>');
        $this->assertEquals('<p>htmlString</p>', $html->__toString());

        // allowed tags are kept
        $html = $this->container->addSanitizedHtml('<p>para <b>bold</b> <a href="https://example.com/">link</a></p>');
        $this->assertEquals('<p>para <b>bold</b> <a href="https://example.com/">link</a></p>', $html->__toString());
    }
    // }}}

    // {{{ testAddSanitizedHtmlRemovesDangerousHtml()
    /**
     * Tests that addSanitizedHtml removes scripts and event handlers.
     **/
    public function testAddSanitizedHtmlRemovesDangerousHtml()
    {
        $html = $this->container->addSanitizedHtml('<script>alert(1)</script><p>htmlString</p>');
        $this->assertEquals('<p>htmlString</p>', $html->__toString());

        $html = $this->container->addSanitizedHtml('<p onclick="alert(1)">htmlString</p>');
        $this->assertEquals('<p>htmlString</p>', $html->__toString());
    }
    // }}}

    // {{{ testAddSanitizedHtmlWithCustomAllowedTags()
    /**
     * Tests addSanitizedHtml method with custom allowed tags.
     **/
    public function testAddSanitizedHtmlWithCustomAllowedTags()
    {
        $html = $this->container->addSanitizedHtml('<div>htmlString</div>', ['div']);
        $this->assertEquals('<div>htmlString</div>', $html->__toString());
    }
    // }}}

    // {{{ testSetRequired()
    /**
     * Tests setting multiple subelements required attribute
     **/
    public function testSetRequired()
    {
        $element1 = new testElement();
        $element2 = new testElement();

        $this->container->addTestElement($element1);
        $this->container->addTestElement($element2);

        $this->container->setRequired(true);

        $this->assertTrue($element1->required);
        $this->assertTrue($element2->required);

        $this->container->setRequired(false);

        $this->assertFalse($element1->required);
        $this->assertFalse($element2->required);
    }
    // }}}

    // {{{ testGetElement()
    /**
     * Get sub-element by name.
     **/
    public function testGetElement()
    {
        $element = new testElement();

        $this->container->addTestElement($element);

        $this->assertEquals($element, $this->container->getElement('testElementName'));
        // method returns false if element not found
        $this->assertFalse($this->container->getElement('bogusInputName'));
    }
    // }}}
}
