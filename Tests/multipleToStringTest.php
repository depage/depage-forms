<?php

use PHPUnit\Framework\TestCase;
use Depage\HtmlForm\Elements\Multiple;

/**
 * Tests for multiple input element rendering.
 **/
class multipleToStringTest extends TestCase
{
    protected $form;
    protected $multiple;

    // {{{ setUp()
    public function setUp(): void
    {
        $this->form = new nameTestForm();
        $this->multiple = new Multiple('elementName', [], $this->form);
    }
    // }}}

    // {{{ testCheckbox()
    /**
     * Element with default setup and checkbox skin.
     **/
    public function testCheckbox()
    {
        $expected = '<p id="formName-elementName" class="input-multiple skin-checkbox" data-errorMessage="Please enter valid data">' .
            '<span class="depage-label">elementName</span>' .
            '<span></span>' .
        '</p>' . "\n";

        $this->assertEquals($expected, $this->multiple->__toString());
    }
    // }}}

    // {{{ testSelect()
    /**
     * Element with default setup and select skin.
     **/
    public function testSelect()
    {
        $expected = '<p id="formName-elementName" class="input-multiple skin-select" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="depage-label">elementName</span>' .
                '<select multiple name="elementName[]"></select>' .
            '</label>' .
        '</p>' . "\n";

        $parameters = ['skin' => 'select'];
        $multiple = new multiple('elementName', $parameters, $this->form);
        $this->assertEquals($expected, $multiple->__toString());
    }
    // }}}

    // {{{ testCheckboxRequired()
    /**
     * Rendered checkbox with 'required' attribute set.
     **/
    public function testCheckboxRequired()
    {
        $expected = '<p id="formName-elementName" class="input-multiple required skin-checkbox" data-errorMessage="Please enter valid data">' .
            '<span class="depage-label">elementName <em>*</em></span>' .
            '<span></span>' .
        '</p>' . "\n";

        $this->multiple->setRequired();
        $this->assertEquals($expected, $this->multiple->__toString());
    }
    // }}}

    // {{{ testSelectRequired()
    /**
     * Rendered select input with 'required' attribute set.
     **/
    public function testSelectRequired()
    {
        $expected = '<p id="formName-elementName" class="input-multiple required skin-select" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="depage-label">elementName <em>*</em></span>' .
                '<select multiple name="elementName[]" required="required"></select>' .
            '</label>' .
        '</p>' . "\n";

        $parameters = ['skin' => 'select'];
        $multiple = new multiple('elementName', $parameters, $this->form);
        $multiple->setRequired();
        $this->assertEquals($expected, $multiple->__toString());
    }
    // }}}

    // {{{ testCheckboxList()
    /**
     * Rendered checkbox with option list
     **/
    public function testCheckboxList()
    {
        $expected = '<p id="formName-elementName" class="input-multiple skin-checkbox" data-errorMessage="Please enter valid data">' .
            '<span class="depage-label">elementName</span>' .
            '<span>' .
                '<span>' .
                    '<label>' .
                        '<input type="checkbox" name="elementName[]" value="0">' .
                        '<span>item1</span>' .
                    '</label>' .
                '</span>' .
                '<span>' .
                    '<label>' .
                        '<input type="checkbox" name="elementName[]" value="1">' .
                        '<span>item2</span>' .
                    '</label>' .
                '</span>' .
                '<span>' .
                    '<label>' .
                        '<input type="checkbox" name="elementName[]" value="2">' .
                        '<span>item3</span>' .
                    '</label>' .
                '</span>' .
            '</span>' .
        '</p>' . "\n";

        $parameters = [
            'list' => ['item1', 'item2', 'item3'],
        ];
        $multiple = new multiple('elementName', $parameters, $this->form);
        $this->assertEquals($expected, $multiple->__toString());
    }
    // }}}

    // {{{ testSelectList()
    /**
     * Rendered select input with option list
     **/
    public function testSelectList()
    {
        $expected = '<p id="formName-elementName" class="input-multiple skin-select" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="depage-label">elementName</span>' .
                '<select multiple name="elementName[]">' .
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
        $multiple = new multiple('elementName', $parameters, $this->form);
        $this->assertEquals($expected, $multiple->__toString());
    }
    // }}}

    // {{{ testCheckboxAssociativeList()
    /**
     * Rendered checkbox input with option list. List Parameters are parsed as
     * associative array.
     **/
    public function testCheckboxAssociativeList()
    {
        $expected = '<p id="formName-elementName" class="input-multiple skin-checkbox" data-errorMessage="Please enter valid data">' .
            '<span class="depage-label">elementName</span>' .
            '<span>' .
                '<span>' .
                    '<label>' .
                        '<input type="checkbox" name="elementName[]" value="key1">' .
                        '<span>item1</span>' .
                    '</label>' .
                '</span>' .
                '<span>' .
                    '<label>' .
                        '<input type="checkbox" name="elementName[]" value="key2">' .
                        '<span>item2</span>' .
                    '</label>' .
                '</span>' .
                '<span>' .
                    '<label>' .
                        '<input type="checkbox" name="elementName[]" value="key3">' .
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
        $multiple = new multiple('elementName', $parameters, $this->form);
        $this->assertEquals($expected, $multiple->__toString());
    }
    // }}}

    // {{{ testSelectAssociativeList()
    /**
     * Rendered select input with option list. List Parameters are parsed as
     * associative array.
     **/
    public function testSelectAssociativeList()
    {
        $expected = '<p id="formName-elementName" class="input-multiple skin-select" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="depage-label">elementName</span>' .
                '<select multiple name="elementName[]">' .
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
        $multiple = new multiple('elementName', $parameters, $this->form);
        $this->assertEquals($expected, $multiple->__toString());
    }
    // }}}

    // {{{ testSelectOptgroups()
    /**
     * Rendered select input with optgroups. List parameters are parsed in
     * two-dimensional array.
     **/
    public function testSelectOptgroups()
    {
        $expected = '<p id="formName-elementName" class="input-multiple skin-select" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="depage-label">elementName</span>' .
                '<select multiple name="elementName[]">' .
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
        $multiple = new multiple('elementName', $parameters, $this->form);
        $this->assertEquals($expected, $multiple->__toString());
    }
    // }}}

    // {{{ testCheckboxHtmlEscaping()
    /**
     * Rendered checkbox; tests html escaping of attributes that can be set by instantiation parameters.
     **/
    public function testCheckboxHtmlEscaping()
    {
        $expected = '<p id="formName-elementName" class="input-multiple required skin-checkbox" title="ti&quot;&gt;tle" data-errorMessage="er&quot;&gt;rorMessage">' .
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
        $multiple = new multiple('elementName', $parameters, $this->form);
        $this->assertEquals($expected, $multiple->__toString());
    }
    // }}}

    // {{{ testSelectHtmlEscaping()
    /**
     * Rendered select input; tests html escaping of attributes that can be set by instantiation parameters.
     **/
    public function testSelectHtmlEscaping()
    {
        $expected = '<p id="formName-elementName" class="input-multiple required skin-select" title="ti&quot;&gt;tle" data-errorMessage="er&quot;&gt;rorMessage">' .
            '<label>' .
                '<span class="depage-label">la&quot;&gt;bel <em>ma&quot;&gt;rker</em></span>' .
                '<select multiple name="elementName[]" required="required"></select>' .
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
        $multiple = new multiple('elementName', $parameters, $this->form);
        $this->assertEquals($expected, $multiple->__toString());
    }
    // }}}

    // {{{ testHtmlCheckboxEscapedList()
    /**
     * Rendered checkbox; tests html escaping of option list.
     **/
    public function testHtmlCheckboxEscapedList()
    {
        $expected = '<p id="formName-elementName" class="input-multiple skin-checkbox" data-errorMessage="Please enter valid data">' .
            '<span class="depage-label">elementName</span>' .
            '<span>' .
                '<span>' .
                    '<label>' .
                        '<input type="checkbox" name="elementName[]" value="0">' .
                        '<span>it&quot;&gt;em1</span>' .
                    '</label>' .
                '</span>' .
                '<span>' .
                    '<label>' .
                        '<input type="checkbox" name="elementName[]" value="1">' .
                        '<span>it&quot;&gt;em2</span>' .
                    '</label>' .
                '</span>' .
                '<span>' .
                    '<label>' .
                        '<input type="checkbox" name="elementName[]" value="2">' .
                        '<span>it&quot;&gt;em3</span>' .
                    '</label>' .
                '</span>' .
            '</span>' .
        '</p>' . "\n";

        $parameters = [
            'list' => ['it">em1', 'it">em2', 'it">em3'],
        ];
        $multiple = new multiple('elementName', $parameters, $this->form);
        $this->assertEquals($expected, $multiple->__toString());
    }
    // }}}

    // {{{ testHtmlSelectEscapedList()
    /**
     * Rendered select input; tests html escaping of option list.
     **/
    public function testHtmlSelectEscapedList()
    {
        $expected = '<p id="formName-elementName" class="input-multiple skin-select" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="depage-label">elementName</span>' .
                '<select multiple name="elementName[]">' .
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
        $multiple = new multiple('elementName', $parameters, $this->form);
        $this->assertEquals($expected, $multiple->__toString());
    }
    // }}}

    // {{{ testCheckboxEscapedAssociativeList()
    /**
     * Rendered checkbox; tests html escaping of associative option list.
     **/
    public function testCheckboxEscapedAssociativeList()
    {
        $expected = '<p id="formName-elementName" class="input-multiple skin-checkbox" data-errorMessage="Please enter valid data">' .
            '<span class="depage-label">elementName</span>' .
            '<span>' .
                '<span>' .
                    '<label>' .
                        '<input type="checkbox" name="elementName[]" value="ke&quot;&gt;y1">' .
                        '<span>it&quot;&gt;em1</span>' .
                    '</label>' .
                '</span>' .
                '<span>' .
                    '<label>' .
                        '<input type="checkbox" name="elementName[]" value="ke&quot;&gt;y2">' .
                        '<span>it&quot;&gt;em2</span>' .
                    '</label>' .
                '</span>' .
                '<span>' .
                    '<label>' .
                        '<input type="checkbox" name="elementName[]" value="ke&quot;&gt;y3">' .
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

        $multiple = new multiple('elementName', $parameters, $this->form);
        $this->assertEquals($expected, $multiple->__toString());
    }
    // }}}

    // {{{ testSelectEscapedAssociativeList()
    /**
     * Rendered select input; tests html escaping of associative option list.
     **/
    public function testSelectEscapedAssociativeList()
    {
        $expected = '<p id="formName-elementName" class="input-multiple skin-select" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="depage-label">elementName</span>' .
                '<select multiple name="elementName[]">' .
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

        $multiple = new multiple('elementName', $parameters, $this->form);
        $this->assertEquals($expected, $multiple->__toString());
    }
    // }}}
}
