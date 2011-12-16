<?php

use depage\htmlform\elements\textarea;

/**
 * Tests for boolean input element rendering.
 **/
class textareaToStringTest extends PHPUnit_Framework_TestCase {
    // {{{ setUp()
    public function setUp() {
        $this->form     = new nameTestForm;
        $this->textarea = new textarea('textareaName', array(), $this->form);
    }
    // }}}

    // {{{ testSimple()
    /**
     * Element with default setup
     **/
    public function testSimple() {
        $expected = '<p id="formName-textareaName" class="input-textarea" data-errorMessage="Please enter valid data" data-textarea-options="{&quot;autogrow&quot;:false}">' .
            '<label>' .
                '<span class="label">textareaName</span>' .
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
    public function testValue() {
        $expected = '<p id="formName-textareaName" class="input-textarea" data-errorMessage="Please enter valid data" data-textarea-options="{&quot;autogrow&quot;:false}">' .
            '<label>' .
                '<span class="label">textareaName</span>' .
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
    public function testRequired() {
        $expected = '<p id="formName-textareaName" class="input-textarea required" data-errorMessage="Please enter valid data" data-textarea-options="{&quot;autogrow&quot;:false}">' .
            '<label>' .
                '<span class="label">textareaName <em>*</em></span>' .
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
    public function testHtmlEscaping() {
        $expected = '<p id="formName-textareaName" class="input-textarea" title="ti&quot;&gt;tle" data-errorMessage="er&quot;&gt;rorMessage" data-textarea-options="{&quot;autogrow&quot;:false}">' .
            '<label>' .
                '<span class="label">la&quot;&gt;bel</span>' .
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
