<?php

use PHPUnit\Framework\TestCase;
use Depage\HtmlForm\Elements\Password;

/**
 * Tests for multiple input element rendering.
 **/
class passwordToStringTest extends TestCase
{
    protected $form;

    // {{{ setUp()
    public function setUp():void
    {
        $this->form = new nameTestForm;
    }
    // }}}

    // {{{ testAutocomplete()
    /**
     * Element with default setup and checkbox skin.
     **/
    public function testAutocompleteDefault()
    {
        $password = new Password('passwordName', [], $this->form);
        $expected = '<p id="formName-passwordName" class="input-password" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="depage-label">passwordName</span>' .
                '<input name="passwordName" type="password" value="">' .
            '</label>' .
        '</p>' . "\n";

        $this->assertEquals($expected, $password->__toString());
    }
    // }}}
    // {{{ testAutocomplete()
    /**
     * Element with default setup and checkbox skin.
     **/
    public function testAutocompleteNewPassword()
    {
        $password = new Password('passwordName', ['autocomplete' => "new-password"], $this->form);
        $expected = '<p id="formName-passwordName" class="input-password" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="depage-label">passwordName</span>' .
                '<input name="passwordName" type="password" autocomplete="new-password" value="">' .
            '</label>' .
        '</p>' . "\n";

        $this->assertEquals($expected, $password->__toString());
    }
    // }}}
}
/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
