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
    // {{{ setUp()
    protected function setUp():void
    {
        $this->form         = new nameTestForm;
        $this->container    = new containerTestClass('containerName', array(), $this->form);
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

    // {{{ testSetRequired()
    /**
     * Tests setting multiple subelements required attribute
     **/
    public function testSetRequired()
    {
        $element1 = new testElement;
        $element2 = new testElement;

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
        $element = new testElement;

        $this->container->addTestElement($element);

        $this->assertEquals($element, $this->container->getElement('testElementName'));
        // method returns false if element not found
        $this->assertFalse($this->container->getElement('bogusInputName'));
    }
    // }}}
}
