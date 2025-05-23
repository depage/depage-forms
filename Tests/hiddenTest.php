<?php

use PHPUnit\Framework\TestCase;
use Depage\HtmlForm\Elements\Hidden;

/**
 * General tests for the hidden input element.
 **/
class hiddenTest extends TestCase
{
    protected $form;
    protected $hidden;

    // {{{ setUp()
    public function setUp(): void
    {
        $this->form     = new nameTestForm();
        $this->hidden   = new Hidden('nameString', [], $this->form);
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
