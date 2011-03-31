<?php
require_once('../abstracts/container.php');

use depage\htmlform\abstracts\container;
use depage\htmlform\exceptions;

class containerTestClass extends container {
    public function __construct($name, $parameters = array()) {
        parent::__construct($name, $parameters);
    }
}

class containerTest extends PHPUnit_Framework_TestCase {
    protected function setUp() {
        $this->container = new containerTestClass('containerNameString');
    }

    public function testContainerConstruct() {
        $this->assertEquals($this->container->getName(), 'containerNameString');
    }

    public function testGetContainerElements() {
        $this->container->addText('text1');
        $this->container->addFieldset('fieldset1');
        $this->container->addText('text2');

        $elements = $this->container->getElements();
        $this->assertEquals($elements[0]->getName(), 'text1');
        $this->assertEquals($elements[1]->getName(), 'text2');
    }

    public function testInvalidContainerNameException() {
        try {
            $input = new containerTestClass(' ', array());
        } catch (exceptions\invalidContainerNameException $expected) {
            return;
        }
        $this->fail('Expected invalidContainerNameException.');
    }

    public function testContainerNameNoStringException() {
        try {
            $input = new containerTestClass(42, array());
        } catch (exceptions\ContainerNameNoStringException $expected) {
            return;
        }
        $this->fail('Expected ContainerNameNoStringException.');
    }
}
