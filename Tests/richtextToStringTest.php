<?php

use PHPUnit\Framework\TestCase;
use Depage\HtmlForm\Elements\Richtext;

/**
 * Tests for boolean input element rendering.
 **/
class richtextToStringTest extends TestCase
{
    protected $form;
    protected $textarea;

    // {{{ setUp()
    public function setUp(): void
    {
        $this->form     = new nameTestForm();
        $this->textarea = new Richtext('textareaName', [], $this->form);
    }
    // }}}

    // {{{ testSimple()
    /**
     * Element with default setup
     **/
    public function testSimple()
    {
        $expected = '<p id="formName-textareaName" class="input-richtext" data-richtext-options="{&quot;stylesheet&quot;:null,&quot;allowedTags&quot;:[&quot;a&quot;,&quot;b&quot;,&quot;strong&quot;,&quot;i&quot;,&quot;em&quot;,&quot;small&quot;,&quot;p&quot;,&quot;br&quot;,&quot;h1&quot;,&quot;h2&quot;,&quot;ul&quot;,&quot;ol&quot;,&quot;li&quot;]}" data-textarea-options="{&quot;autogrow&quot;:true}" data-errorMessage="Please enter valid data">' .
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
        $expected = '<p id="formName-textareaName" class="input-richtext" data-richtext-options="{&quot;stylesheet&quot;:null,&quot;allowedTags&quot;:[&quot;a&quot;,&quot;b&quot;,&quot;strong&quot;,&quot;i&quot;,&quot;em&quot;,&quot;small&quot;,&quot;p&quot;,&quot;br&quot;,&quot;h1&quot;,&quot;h2&quot;,&quot;ul&quot;,&quot;ol&quot;,&quot;li&quot;]}" data-textarea-options="{&quot;autogrow&quot;:true}" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="depage-label">textareaName</span>' .
                '<textarea name="textareaName">&lt;p&gt;testValue&lt;/p&gt;' . "\n" . '</textarea>' .
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
        $expected = '<p id="formName-textareaName" class="input-richtext required" data-richtext-options="{&quot;stylesheet&quot;:null,&quot;allowedTags&quot;:[&quot;a&quot;,&quot;b&quot;,&quot;strong&quot;,&quot;i&quot;,&quot;em&quot;,&quot;small&quot;,&quot;p&quot;,&quot;br&quot;,&quot;h1&quot;,&quot;h2&quot;,&quot;ul&quot;,&quot;ol&quot;,&quot;li&quot;]}" data-textarea-options="{&quot;autogrow&quot;:true}" data-errorMessage="Please enter valid data">' .
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
        $expected = '<p id="formName-textareaName" class="input-richtext" title="ti&quot;&gt;tle" data-richtext-options="{&quot;stylesheet&quot;:null,&quot;allowedTags&quot;:[&quot;a&quot;,&quot;b&quot;,&quot;strong&quot;,&quot;i&quot;,&quot;em&quot;,&quot;small&quot;,&quot;p&quot;,&quot;br&quot;,&quot;h1&quot;,&quot;h2&quot;,&quot;ul&quot;,&quot;ol&quot;,&quot;li&quot;]}" data-textarea-options="{&quot;autogrow&quot;:true}" data-errorMessage="er&quot;&gt;rorMessage">' .
            '<label>' .
                '<span class="depage-label">la&quot;&gt;bel</span>' .
                '<textarea name="textareaName"></textarea>' .
            '</label>' .
        '</p>' . "\n";

        $parameters = [
            'label'         => 'la">bel',
            'marker'        => 'ma">rker',
            'errorMessage'  => 'er">rorMessage',
            'title'         => 'ti">tle',
        ];
        $textarea = new Richtext('textareaName', $parameters, $this->form);
        $this->assertEquals($expected, $textarea->__toString());
    }
    // }}}
}
