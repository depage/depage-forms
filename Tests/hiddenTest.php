<?php

use Depage\HtmlForm\Elements\Hidden;

/**
 * General tests for the hidden input element.
 **/
class hiddenTest extends PHPUnit_Framework_TestCase
{
    // {{{ setUp()
    public function setUp()
    {
        $this->form     = new nameTestForm;
        $this->hidden   = new Hidden('nameString', array(), $this->form);
    }
    // }}}

    // {{{ testHiddenSetValue()
    /**
     * Tests setValue method.
     **/
    public function testHiddenSetValue()
    {
        $this->hidden->setValue('valueString');
        $this->assertEquals('valueString', $this->hidden->getValue());
    }
    // }}}
}
