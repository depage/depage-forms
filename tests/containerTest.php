<?php

use depage\htmlform\abstracts\container;
use depage\htmlform\exceptions;

/**
 * Container is abstract, so we need this test class to instantiate it.
 **/
class containerTestClass extends container {
    // needed for testSetRequired()
    public function addTestElement($element) {
        $this->elements[] = $element;
    }
}

/**
 * needed for testSetRequired()
 **/
class testElement {
    public $required = false;

    public function setRequired($required) {
        $this->required = $required;
    }
}

/**
 * General tests for the container class.
 **/
class containerTest extends PHPUnit_Framework_TestCase {
    protected function setUp() {
        $this->form         = new nameTestForm;
        $this->container    = new containerTestClass('containerName', array(), $this->form);
    }

    /**
     * Constructor test.
     **/
    public function testConstruct() {
        $this->assertEquals('containerName', $this->container->getName());
    }

    /**
     * Tests method to get subelements of container.
     **/
    public function testGetElements() {
        $text1      = $this->container->addText('text1');
        $fieldset   = $this->container->addFieldset('fieldset');
        $text2      = $this->container->addText('text2');

        // input elements only
        $elements = $this->container->getElements();
        $this->assertEquals($text1, $elements[0]);
        $this->assertEquals($text2, $elements[1]);

        // elements including other container elements
        $elements = $this->container->getElements(true);
        $this->assertEquals($text1, $elements[0]);
        $this->assertEquals($fieldset, $elements[1]);
        $this->assertEquals($text2, $elements[2]);
    }

    /**
     * Exception on unknown element type.
     **/
    public function test_checkElementType() {
        try {
            $this->container->addElement('bogusType', 'elementName');
        } catch (exceptions\unknownElementTypeException $expected) {
            return;
        }
        $this->fail('Expected unknownElementTypeException.');
    }

    public function testAddHtml() {
        $html = $this->container->addHtml('htmlString');
        $this->assertEquals('htmlString', $html->__toString());
    }

    public function testSetRequired() {
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
}
