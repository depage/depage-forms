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

    public function testInvalidContainerNameException() {
        try {
            $input = new containerTestClass(' ', array());
        }
        catch (exceptions\invalidContainerNameException $expected) {
            return;
        }
        $this->fail('Expected invalidContainerNameException.');
    }

    public function testContainerNameNoStringException() {
        try {
            $input = new containerTestClass(42, array());
        }
        catch (exceptions\ContainerNameNoStringException $expected) {
            return;
        }
        $this->fail('Expected ContainerNameNoStringException.');
    }
}
