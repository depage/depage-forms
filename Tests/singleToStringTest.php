<?php

use PHPUnit\Framework\TestCase;
use Depage\HtmlForm\Elements\Single;

/**
 * Tests for multiple input element rendering.
 **/
class singleToStringTest extends TestCase
{
    protected $form;
    protected $single;

    // {{{ setUp()
    public function setUp(): void
    {
        $this->form     = new nameTestForm();
        $this->single   = new Single('singleName', [], $this->form);
    }
    // }}}

    // {{{ testRadio()
    /**
     * Element with default setup and radio skin.
     **/
    public function testRadio()
    {
        $expected = '<p id="formName-singleName" class="input-single skin-radio" data-errorMessage="Please enter valid data">' .
            '<span class="depage-label">singleName</span>' .
            '<span></span>' .
        '</p>' . "\n";

        $this->assertEquals($expected, $this->single->__toString());
    }
    // }}}

    // {{{ testSelect()
    /**
     * Element with default setup and "select" skin.
     **/
    public function testSelect()
    {
        $expected = '<p id="formName-singleName" class="input-single skin-select" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="depage-label">singleName</span>' .
                '<select name="singleName"></select>' .
            '</label>' .
        '</p>' . "\n";

        $parameters = ['skin' => 'select'];
        $single = new single('singleName', $parameters, $this->form);
        $this->assertEquals($expected, $single->__toString());
    }
    // }}}

    // {{{ testRadioRequired()
    /**
     * Rendered radio input with 'required' attribute set.
     **/
    public function testRadioRequired()
    {
        $expected = '<p id="formName-singleName" class="input-single required skin-radio" data-errorMessage="Please enter valid data">' .
            '<span class="depage-label">singleName <em>*</em></span>' .
            '<span></span>' .
        '</p>' . "\n";

        $this->single->setRequired();
        $this->assertEquals($expected, $this->single->__toString());
    }
    // }}}

    // {{{ testSelectRequired()
    /**
     * Rendered "select" input with 'required' attribute set.
     **/
    public function testSelectRequired()
    {
        $expected = '<p id="formName-singleName" class="input-single required skin-select" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="depage-label">singleName <em>*</em></span>' .
                '<select name="singleName" required="required"></select>' .
            '</label>' .
        '</p>' . "\n";

        $parameters = ['skin' => 'select'];
        $single = new single('singleName', $parameters, $this->form);
        $single->setRequired();
        $this->assertEquals($expected, $single->__toString());
    }
    // }}}

    // {{{ testRadioList()
    /**
     * Rendered radio input with option list
     **/
    public function testRadioList()
    {
        $expected = '<p id="formName-singleName" class="input-single skin-radio" data-errorMessage="Please enter valid data">' .
            '<span class="depage-label">singleName</span>' .
            '<span>' .
                '<span>' .
                    '<label class="input-single-option-0" title="item1">' .
                        '<input type="radio" name="singleName" value="0">' .
                        '<span>item1</span>' .
                    '</label>' .
                '</span>' .
                '<span>' .
                    '<label class="input-single-option-1" title="item2">' .
                        '<input type="radio" name="singleName" value="1">' .
                        '<span>item2</span>' .
                    '</label>' .
                '</span>' .
                '<span>' .
                    '<label class="input-single-option-2" title="item3">' .
                        '<input type="radio" name="singleName" value="2">' .
                        '<span>item3</span>' .
                    '</label>' .
                '</span>' .
            '</span>' .
        '</p>' . "\n";

        $parameters = [
            'list' => ['item1', 'item2', 'item3'],
        ];
        $single = new single('singleName', $parameters, $this->form);
        $this->assertEquals($expected, $single->__toString());
    }
    // }}}

    // {{{ testSelectList()
    /**
     * Rendered "select" input with option list
     **/
    public function testSelectList()
    {
        $expected = '<p id="formName-singleName" class="input-single skin-select" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="depage-label">singleName</span>' .
                '<select name="singleName">' .
                    '<option value="0">item1</option>' .
                    '<option value="1">item2</option>' .
                    '<option value="2">item3</option>' .
                '</select>' .
            '</label>' .
        '</p>' . "\n";

        $parameters = [
            'skin' => 'select',
            'list' => ['item1', 'item2', 'item3'],
        ];
        $single = new single('singleName', $parameters, $this->form);
        $this->assertEquals($expected, $single->__toString());
    }
    // }}}

    // {{{ testRadioAssociativeList()
    /**
     * Rendered radio input with option list. List Parameters are parsed as
     * associative array.
     **/
    public function testRadioAssociativeList()
    {
        $expected = '<p id="formName-singleName" class="input-single skin-radio" data-errorMessage="Please enter valid data">' .
            '<span class="depage-label">singleName</span>' .
            '<span>' .
                '<span>' .
                    '<label class="input-single-option-key1" title="item1">' .
                        '<input type="radio" name="singleName" value="key1">' .
                        '<span>item1</span>' .
                    '</label>' .
                '</span>' .
                '<span>' .
                    '<label class="input-single-option-key2" title="item2">' .
                        '<input type="radio" name="singleName" value="key2">' .
                        '<span>item2</span>' .
                    '</label>' .
                '</span>' .
                '<span>' .
                    '<label class="input-single-option-key3" title="item3">' .
                        '<input type="radio" name="singleName" value="key3">' .
                        '<span>item3</span>' .
                    '</label>' .
                '</span>' .
            '</span>' .
        '</p>' . "\n";

        $parameters = [
            'list' => [
                'key1' => 'item1',
                'key2' => 'item2',
                'key3' => 'item3',
            ],
        ];
        $single = new single('singleName', $parameters, $this->form);
        $this->assertEquals($expected, $single->__toString());
    }
    // }}}

    // {{{ testSelectAssociativeList()
    /**
     * Rendered "select" input with option list. List Parameters are parsed as
     * associative array.
     **/
    public function testSelectAssociativeList()
    {
        $expected = '<p id="formName-singleName" class="input-single skin-select" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="depage-label">singleName</span>' .
                '<select name="singleName">' .
                    '<option value="key1">item1</option>' .
                    '<option value="key2">item2</option>' .
                    '<option value="key3">item3</option>' .
                '</select>' .
            '</label>' .
        '</p>' . "\n";

        $parameters = [
            'skin' => 'select',
            'list' => [
                'key1' => 'item1',
                'key2' => 'item2',
                'key3' => 'item3',
            ],
        ];
        $single = new single('singleName', $parameters, $this->form);
        $this->assertEquals($expected, $single->__toString());
    }
    // }}}

    // {{{ testSelectOptgroups()
    /**
     * Rendered select input with optgroups. List parameters are parsed in
     * two-dimensional array.
     **/
    public function testSelectOptgroups()
    {
        $expected = '<p id="formName-singleName" class="input-single skin-select" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="depage-label">singleName</span>' .
                '<select name="singleName">' .
                    '<optgroup label="group1">' .
                        '<option value="key1">item1</option>' .
                        '<option value="key2">item2</option>' .
                    '</optgroup>' .
                    '<optgroup label="group2">' .
                        '<option value="key3">item3</option>' .
                    '</optgroup>' .
                    '<option value="key4">item4</option>' .
                '</select>' .
            '</label>' .
        '</p>' . "\n";

        $parameters = [
            'skin' => 'select',
            'list' => [
                'group1' => [
                    'key1' => 'item1',
                    'key2' => 'item2',
                ],
                'group2' => [
                    'key3' => 'item3',
                ],
                'key4' => 'item4',
            ],
        ];
        $single = new single('singleName', $parameters, $this->form);
        $this->assertEquals($expected, $single->__toString());
    }
    // }}}

    // {{{ testRadioHtmlEscaping()
    /**
     * Rendered radio input; tests html escaping of attributes that can be set by instantiation parameters.
     **/
    public function testRadioHtmlEscaping()
    {
        $expected = '<p id="formName-singleName" class="input-single required skin-radio" title="ti&quot;&gt;tle" data-errorMessage="er&quot;&gt;rorMessage">' .
            '<span class="depage-label">la&quot;&gt;bel <em>ma&quot;&gt;rker</em></span>' .
            '<span></span>' .
        '</p>' . "\n";

        $parameters = [
            'label'         => 'la">bel',
            'marker'        => 'ma">rker',
            'errorMessage'  => 'er">rorMessage',
            'title'         => 'ti">tle',
            'required'      => true,
        ];
        $single = new single('singleName', $parameters, $this->form);
        $this->assertEquals($expected, $single->__toString());
    }
    // }}}

    // {{{ testSelectHtmlEscaping()
    /**
     * Rendered "select" input; tests html escaping of attributes that can be set by instantiation parameters.
     **/
    public function testSelectHtmlEscaping()
    {
        $expected = '<p id="formName-singleName" class="input-single required skin-select" title="ti&quot;&gt;tle" data-errorMessage="er&quot;&gt;rorMessage">' .
            '<label>' .
                '<span class="depage-label">la&quot;&gt;bel <em>ma&quot;&gt;rker</em></span>' .
                '<select name="singleName" required="required"></select>' .
            '</label>' .
        '</p>' . "\n";

        $parameters = [
            'label'         => 'la">bel',
            'marker'        => 'ma">rker',
            'errorMessage'  => 'er">rorMessage',
            'title'         => 'ti">tle',
            'skin'          => 'select',
            'required'      => true,
        ];
        $single = new single('singleName', $parameters, $this->form);
        $this->assertEquals($expected, $single->__toString());
    }
    // }}}

    // {{{ testHtmlRadioEscapedList()
    /**
     * Rendered radio input; tests html escaping of option list.
     **/
    public function testHtmlRadioEscapedList()
    {
        $expected = '<p id="formName-singleName" class="input-single skin-radio" data-errorMessage="Please enter valid data">' .
            '<span class="depage-label">singleName</span>' .
            '<span>' .
                '<span>' .
                    '<label class="input-single-option-0" title="it&quot;&gt;em1">' .
                        '<input type="radio" name="singleName" value="0">' .
                        '<span>it&quot;&gt;em1</span>' .
                    '</label>' .
                '</span>' .
                '<span>' .
                    '<label class="input-single-option-1" title="it&quot;&gt;em2">' .
                        '<input type="radio" name="singleName" value="1">' .
                        '<span>it&quot;&gt;em2</span>' .
                    '</label>' .
                '</span>' .
                '<span>' .
                    '<label class="input-single-option-2" title="it&quot;&gt;em3">' .
                        '<input type="radio" name="singleName" value="2">' .
                        '<span>it&quot;&gt;em3</span>' .
                    '</label>' .
                '</span>' .
            '</span>' .
        '</p>' . "\n";

        $parameters = [
            'list' => ['it">em1', 'it">em2', 'it">em3'],
        ];
        $single = new single('singleName', $parameters, $this->form);
        $this->assertEquals($expected, $single->__toString());
    }
    // }}}

    // {{{ testHtmlSelectEscapedList()
    /**
     * Rendered select input; tests html escaping of option list.
     **/
    public function testHtmlSelectEscapedList()
    {
        $expected = '<p id="formName-singleName" class="input-single skin-select" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="depage-label">singleName</span>' .
                '<select name="singleName">' .
                    '<option value="0">it&quot;&gt;em1</option>' .
                    '<option value="1">it&quot;&gt;em2</option>' .
                    '<option value="2">it&quot;&gt;em3</option>' .
                '</select>' .
            '</label>' .
        '</p>' . "\n";

        $parameters = [
            'list' => ['it">em1', 'it">em2', 'it">em3'],
            'skin' => 'select',
        ];
        $single = new single('singleName', $parameters, $this->form);
        $this->assertEquals($expected, $single->__toString());
    }
    // }}}

    // {{{ testRadioEscapedAssociativeList()
    /**
     * Rendered radio input; tests html escaping of associative option list.
     **/
    public function testRadioEscapedAssociativeList()
    {
        $expected = '<p id="formName-singleName" class="input-single skin-radio" data-errorMessage="Please enter valid data">' .
            '<span class="depage-label">singleName</span>' .
            '<span>' .
                '<span>' .
                    '<label class="input-single-option-ke&amp;quot;&amp;gt;y1" title="it&quot;&gt;em1">' .
                        '<input type="radio" name="singleName" value="ke&quot;&gt;y1">' .
                        '<span>it&quot;&gt;em1</span>' .
                    '</label>' .
                '</span>' .
                '<span>' .
                    '<label class="input-single-option-ke&amp;quot;&amp;gt;y2" title="it&quot;&gt;em2">' .
                        '<input type="radio" name="singleName" value="ke&quot;&gt;y2">' .
                        '<span>it&quot;&gt;em2</span>' .
                    '</label>' .
                '</span>' .
                '<span>' .
                    '<label class="input-single-option-ke&amp;quot;&amp;gt;y3" title="it&quot;&gt;em3">' .
                        '<input type="radio" name="singleName" value="ke&quot;&gt;y3">' .
                        '<span>it&quot;&gt;em3</span>' .
                    '</label>' .
                '</span>' .
            '</span>' .
        '</p>' . "\n";

        $parameters = [
            'list' => [
                'ke">y1' => 'it">em1',
                'ke">y2' => 'it">em2',
                'ke">y3' => 'it">em3',
            ],
        ];

        $single = new single('singleName', $parameters, $this->form);
        $this->assertEquals($expected, $single->__toString());
    }
    // }}}

    // {{{ testSelectEscapedAssociativeList()
    /**
     * Rendered "select" input; tests html escaping of associative option list.
     **/
    public function testSelectEscapedAssociativeList()
    {
        $expected = '<p id="formName-singleName" class="input-single skin-select" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="depage-label">singleName</span>' .
                '<select name="singleName">' .
                    '<option value="ke&quot;&gt;y1">it&quot;&gt;em1</option>' .
                    '<option value="ke&quot;&gt;y2">it&quot;&gt;em2</option>' .
                    '<option value="ke&quot;&gt;y3">it&quot;&gt;em3</option>' .
                '</select>' .
            '</label>' .
        '</p>' . "\n";

        $parameters = [
            'skin' => 'select',
            'list' => [
                'ke">y1' => 'it">em1',
                'ke">y2' => 'it">em2',
                'ke">y3' => 'it">em3',
            ],
        ];

        $single = new single('singleName', $parameters, $this->form);
        $this->assertEquals($expected, $single->__toString());
    }
    // }}}
}
