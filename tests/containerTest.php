<?php

use depage\htmlform\abstracts\container;
use depage\htmlform\exceptions;

class containerTestClass extends container {
    // needed to instantiate (container is abstract)
    public function __construct($name, $parameters = array()) {
        parent::__construct($name, $parameters);
    }

    // needed for testSetRequired()
    public function addTestElement($element) {
        $this->elements[] = $element;
    }

    // needed for testLog() (container::log() is protected)
    public function log($argument, $type) {
        parent::log($argument, $type);
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
 * needed to convert undefined method error to catchable exception.
 **/
class containerTest extends PHPUnit_Framework_TestCase {
    protected function setUp() {
        $this->container = new containerTestClass('containerNameString');
    }

    public function testContainerConstruct() {
        $this->assertEquals($this->container->getName(), 'containerNameString');
    }

    public function testContainerGetElements() {
        $this->container->addText('text1');
        $this->container->addFieldset('fieldset1');
        $this->container->addText('text2');

        $elements = $this->container->getElements();
        $this->assertEquals($elements[0]->getName(), 'text1');
        $this->assertEquals($elements[1]->getName(), 'text2');
    }

    public function testInvalidContainerNameException() {
        try {
            new containerTestClass(' ', array());
        } catch (exceptions\invalidContainerNameException $expected) {
            return;
        }
        $this->fail('Expected invalidContainerNameException.');
    }

    public function testContainerNameNoStringException() {
        try {
            new containerTestClass(42, array());
        } catch (exceptions\containerNameNoStringException $expected) {
            return;
        }
        $this->fail('Expected containerNameNoStringException.');
    }

    public function test_checkElementType() {
        try {
            $this->container->addElement('bogusType', 'elementName');
        } catch (exceptions\unknownInputTypeException $expected) {
            return;
        }
        $this->fail('Expected unknownInputTypeException.');
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

    public function testLog() {
        $log        = new logTestClass;

        $parameters = array('log' => $log);
        $container  = new containerTestClass('containerName', $parameters);

        $container->log('argumentString', 'typeString');

        $expected = array(
            'argument'  => 'argumentString',
            'type'      => 'typeString',
        );

        $this->assertEquals($expected, $log->error);
    }

    public function undefinedMethodHandler($errno) {
            if ($errno = 256) throw new undefinedMethodException;
            return;
        }

    public function testUndefinedMethodError() {
        set_error_handler(array($this, 'undefinedMethodHandler'));

        try {
            $this->container->__call('undefined', 'argumentString');
        } catch (undefinedMethodException $expected) {
            restore_error_handler();
            return;
        }
        $this->fail('Expected undefinedMethodException');
    }
}
