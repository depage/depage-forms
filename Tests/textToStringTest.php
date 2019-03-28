<?php

use PHPUnit\Framework\TestCase;
use Depage\HtmlForm\Elements\Text;

/**
 * Tests for multiple input element rendering.
 **/
class textToStringTest extends TestCase
{
    // {{{ setUp()
    public function setUp():void
    {
        $this->form = new nameTestForm;
        $this->text = new Text('textName', array(), $this->form);
    }
    // }}}

    // {{{ testSimple()
    /**
     * Element with default setup and checkbox skin.
     **/
    public function testSimple()
    {
        $expected = '<p id="formName-textName" class="input-text" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="depage-label">textName</span>' .
                '<input name="textName" type="text" value="">' .
            '</label>' .
        '</p>' . "\n";

        $this->assertEquals($expected, $this->text->__toString());
    }
    // }}}

    // {{{ testValue()
    /**
     * Element with default setup and select skin.
     **/
    public function testValue()
    {
        $expected = '<p id="formName-textName" class="input-text" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="depage-label">textName</span>' .
                '<input name="textName" type="text" value="testValue">' .
            '</label>' .
        '</p>' . "\n";

        $this->text->setValue('testValue');
        $this->assertEquals($expected, $this->text->__toString());
    }
    // }}}

    // {{{ testRequired()
    /**
     * Rendered element with 'required' attribute set.
     **/
    public function testRequired()
    {
        $expected = '<p id="formName-textName" class="input-text required" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="depage-label">textName <em>*</em></span>' .
                '<input name="textName" type="text" required="required" value="">' .
            '</label>' .
        '</p>' . "\n";

        $this->text->setRequired();
        $this->assertEquals($expected, $this->text->__toString());
    }
    // }}}

    // {{{ testAttributes()
    /**
     * Rendered element with 'auto*' attributes set.
     **/
    public function testAttributes()
    {
        $autoAttributes = array(
            "autocapitalize",
            "autocomplete",
            "autocorrect",
        );

        foreach ($autoAttributes as $attr) {
            foreach (array(null, true, false) as $status) {
                $this->form = new nameTestForm;
                $this->text = new text('textName', array(
                    $attr => $status,
                ), $this->form);

                if (is_null($status)) {
                    $attribute = "";
                } elseif (!$status) {
                    $attribute = " $attr=\"off\"";
                } elseif ($status) {
                    $attribute = " $attr=\"on\"";
                }
                $expected = '<p id="formName-textName" class="input-text required" data-errorMessage="Please enter valid data">' .
                    '<label>' .
                        '<span class="depage-label">textName <em>*</em></span>' .
                        '<input name="textName" type="text" required="required"' . $attribute . ' value="">' .
                    '</label>' .
                '</p>' . "\n";

                $this->text->setRequired();
                $this->assertEquals($expected, $this->text->__toString());
            }
        }
    }
    // }}}

    // {{{ testList()
    /**
     * Rendered element with option list
     **/
    public function testList()
    {
        $expected ='<p id="formName-textName" class="input-text" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="depage-label">textName</span>' .
                '<input name="textName" type="text" list="formName-textName-list" value="">' .
                '<datalist id="formName-textName-list">' .
                    '<option value="item1">' .
                    '<option value="item2">' .
                    '<option value="item3">' .
                '</datalist>' .
            '</label>' .
        '</p>' . "\n";

        $parameters = array(
            'list' => array('item1', 'item2', 'item3')
        );
        $text = new text('textName', $parameters, $this->form);
        $this->assertEquals($expected, $text->__toString());
    }
    // }}}

    // {{{ testAssociativeList()
    /**
     * Rendered element with option list. List Parameters are parsed as
     * associative array.
     **/
    public function testAssociativeList()
    {
        $expected = '<p id="formName-textName" class="input-text" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="depage-label">textName</span>' .
                '<input name="textName" type="text" list="formName-textName-list" value="">' .
                '<datalist id="formName-textName-list">' .
                    '<option value="key1" label="item1">' .
                    '<option value="key2" label="item2">' .
                    '<option value="key3" label="item3">' .
                '</datalist>' .
            '</label>' .
        '</p>'. "\n";

        $parameters = array(
            'list' => array(
                'key1' => 'item1',
                'key2' => 'item2',
                'key3' => 'item3',
            )
        );
        $text = new text('textName', $parameters, $this->form);
        $this->assertEquals($expected, $text->__toString());
    }
    // }}}

    // {{{ testHtmlEscaping()
    /**
     * Rendered element; tests html escaping of attributes that can be set by instantiation parameters.
     **/
    public function testHtmlEscaping()
    {
        $expected = '<p id="formName-textName" class="input-text required" title="ti&quot;&gt;tle" lang="la&quot;&gt;ng" data-errorMessage="er&quot;&gt;rorMessage">' .
            '<label>' .
                '<span class="depage-label">la&quot;&gt;bel <em>ma&quot;&gt;rker</em></span>' .
                '<input name="textName" type="text" required="required" value="val&quot;&gt;ue&lt;&#039;">' .
            '</label>' .
        '</p>' . "\n";

        $parameters = array(
            'label'         => 'la">bel',
            'marker'        => 'ma">rker',
            'errorMessage'  => 'er">rorMessage',
            'title'         => 'ti">tle',
            'lang'         => 'la">ng',
            'required'      => true,
        );
        $text = new text('textName', $parameters, $this->form);
        $text->setValue("val\">ue<'");
        $this->assertEquals($expected, $text->__toString());
    }
    // }}}

    // {{{ testHtmlEscapedList()
    /**
     * Rendered element; tests html escaping of option list.
     **/
    public function testHtmlEscapedList()
    {
        $expected = '<p id="formName-textName" class="input-text" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="depage-label">textName</span>' .
                '<input name="textName" type="text" list="formName-textName-list" value="">' .
                '<datalist id="formName-textName-list">' .
                    '<option value="it&quot;&gt;em1">' .
                    '<option value="it&quot;&gt;em2">' .
                    '<option value="it&quot;&gt;em3">' .
                '</datalist>' .
            '</label>' .
        '</p>' . "\n";

        $parameters = array(
            'list' => array('it">em1', 'it">em2', 'it">em3'),
        );
        $text = new text('textName', $parameters, $this->form);
        $this->assertEquals($expected, $text->__toString());
    }
    // }}}

    // {{{ testEscapedAssociativeList()
    /**
     * Rendered element; tests html escaping of associative option list.
     **/
     public function testEscapedAssociativeList()
     {
        $expected = '<p id="formName-textName" class="input-text" data-errorMessage="Please enter valid data">' .
            '<label>' .
                '<span class="depage-label">textName</span>' .
                '<input name="textName" type="text" list="formName-textName-list" value="">' .
                '<datalist id="formName-textName-list">' .
                    '<option value="ke&quot;&gt;y1" label="it&quot;&gt;em1">' .
                    '<option value="ke&quot;&gt;y2" label="it&quot;&gt;em2">' .
                    '<option value="ke&quot;&gt;y3" label="it&quot;&gt;em3">' .
                '</datalist>' .
            '</label>' .
        '</p>'. "\n";

        $parameters = array(
            'list' => array(
                'ke">y1' => 'it">em1',
                'ke">y2' => 'it">em2',
                'ke">y3' => 'it">em3',
            )
        );

        $text = new text('textName', $parameters, $this->form);
        $this->assertEquals($expected, $text->__toString());
    }
    // }}}
}
/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
