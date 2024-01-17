<?php

use PHPUnit\Framework\TestCase;
use Depage\HtmlForm\Elements\Textarea;

/**
 * Tests for boolean input element rendering.
 **/
class textareaToStringTest extends TestCase
{
    protected $form;
    protected $textarea;

    // {{{ setUp()
    public function setUp():void
    {
        $this->form     = new nameTestForm;
        $this->textarea = new Textarea('textareaName', array(), $this->form);
    }
    // }}}

    // {{{ testSimple()
    /**
     * Element with default setup
     **/
    public function testSimple()
    {
        $expected = '<p id="formName-textareaName" class="input-textarea" data-textarea-options="{&quot;autogrow&quot;:false}" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="depage-label">textareaName</span>' .
                '<textarea name="textareaName"></textarea>' .
            '</label>' .
        '</p>' . "\n";

        $this->assertEquals($expected, $this->textarea->__toString());
    }
    // }}}

    // {{{ testValue()
    /**
     * Tests rendering with value set.
     **/
    public function testValue()
    {
        $expected = '<p id="formName-textareaName" class="input-textarea" data-textarea-options="{&quot;autogrow&quot;:false}" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="depage-label">textareaName</span>' .
                '<textarea name="textareaName">testValue</textarea>' .
            '</label>' .
        '</p>' . "\n";

        $this->textarea->setValue('testValue');
        $this->assertEquals($expected, $this->textarea->__toString());
    }
    // }}}

    // {{{ testRequired()
    /**
     * Rendered element with set required attribute
     **/
    public function testRequired()
    {
        $expected = '<p id="formName-textareaName" class="input-textarea required" data-textarea-options="{&quot;autogrow&quot;:false}" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="depage-label">textareaName <em>*</em></span>' .
                '<textarea name="textareaName" required="required"></textarea>' .
            '</label>' .
        '</p>' . "\n";

        $this->textarea->setRequired();
        $this->assertEquals($expected, $this->textarea->__toString());
    }
    // }}}

    // {{{ testHtmlEscaping()
    /**
     * Tests html escaping of attributes that can be set by instantiation parameters
     **/
    public function testHtmlEscaping()
    {
        $expected = '<p id="formName-textareaName" class="input-textarea" title="ti&quot;&gt;tle" data-textarea-options="{&quot;autogrow&quot;:false}" data-errorMessage="er&quot;&gt;rorMessage">' .
            '<label>' .
                '<span class="depage-label">la&quot;&gt;bel</span>' .
                '<textarea name="textareaName"></textarea>' .
            '</label>' .
        '</p>' . "\n";

        $parameters = array(
            'label'         => 'la">bel',
            'marker'        => 'ma">rker',
            'errorMessage'  => 'er">rorMessage',
            'title'         => 'ti">tle',
        );
        $textarea = new textarea('textareaName', $parameters, $this->form);
        $this->assertEquals($expected, $textarea->__toString());
    }
    // }}}
}
