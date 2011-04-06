<?php

use depage\htmlform\elements\multiple;

class multipleToStringTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->form = new nameTestForm;
        $this->multiple = new multiple('elementName', array(), $this->form);
    }

    public function testCheckbox() {
        $expected = '<p id="formName-elementName" class="input-multiple" data-errorMessage="Please enter valid data!">' .
            '<span class="label">elementName</span>' .
            '<span></span>' .
        '</p>' . "\n";

        $this->assertEquals($expected, $this->multiple->__toString());
    }

    public function testSelect() {
        $expected = '<p id="formName-elementName" class="input-multiple" data-errorMessage="Please enter valid data!">' .
            '<label>' .
                '<span class="label">elementName</span>' .
                '<select multiple name="elementName[]"></select>' .
            '</label>' .
        '</p>' . "\n";

        $parameters = array('skin' => 'select');
        $multiple = new multiple('elementName', $parameters, $this->form);
        $this->assertEquals($expected, $multiple->__toString());
    }

    public function testCheckboxRequired() {
        $expected = '<p id="formName-elementName" class="input-multiple required" data-errorMessage="Please enter valid data!">' .
            '<span class="label">elementName <em>*</em></span>' .
            '<span></span>' .
        '</p>' . "\n";

        $this->multiple->setRequired();
        $this->assertEquals($expected, $this->multiple->__toString());
    }

    public function testSelectRequired() {
        $expected = '<p id="formName-elementName" class="input-multiple required" data-errorMessage="Please enter valid data!">' .
            '<label>' .
                '<span class="label">elementName <em>*</em></span>' .
                '<select multiple name="elementName[]"></select>' .
            '</label>' .
        '</p>' . "\n";

        $parameters = array('skin' => 'select');
        $multiple = new multiple('elementName', $parameters, $this->form);
        $multiple->setRequired();
        $this->assertEquals($expected, $multiple->__toString());
    }

    public function testCheckboxList() {
        $expected = '<p id="formName-elementName" class="input-multiple" data-errorMessage="Please enter valid data!">' .
            '<span class="label">elementName</span>' .
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

        $parameters = array(
            'list' => array('item1', 'item2', 'item3')
        );
        $multiple = new multiple('elementName', $parameters, $this->form);
        $this->assertEquals($expected, $multiple->__toString());
    }

    public function testSelectList() {
        $expected = '<p id="formName-elementName" class="input-multiple" data-errorMessage="Please enter valid data!">' .
            '<label>' .
                '<span class="label">elementName</span>' .
                '<select multiple name="elementName[]">' .
                    '<option value="0">item1</option>' .
                    '<option value="1">item2</option>' .
                    '<option value="2">item3</option>' .
                '</select>' .
            '</label>' .
        '</p>' . "\n";

        $parameters = array(
            'skin' => 'select',
            'list' => array('item1', 'item2', 'item3')
        );
        $multiple = new multiple('elementName', $parameters, $this->form);
        $this->assertEquals($expected, $multiple->__toString());
    }

    public function testCheckboxAssociativeList() {
        $expected = '<p id="formName-elementName" class="input-multiple" data-errorMessage="Please enter valid data!">' .
            '<span class="label">elementName</span>' .
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

        $parameters = array(
            'list' => array(
                'key1' => 'item1',
                'key2' => 'item2',
                'key3' => 'item3',
            )
        );
        $multiple = new multiple('elementName', $parameters, $this->form);
        $this->assertEquals($expected, $multiple->__toString());
    }

    public function testSelectAssociativeList() {
        $expected = '<p id="formName-elementName" class="input-multiple" data-errorMessage="Please enter valid data!">' .
            '<label>' .
                '<span class="label">elementName</span>' .
                '<select multiple name="elementName[]">' .
                    '<option value="key1">item1</option>' .
                    '<option value="key2">item2</option>' .
                    '<option value="key3">item3</option>' .
                '</select>' .
            '</label>' .
        '</p>' . "\n";

        $parameters = array(
            'skin' => 'select',
            'list' => array(
                'key1' => 'item1',
                'key2' => 'item2',
                'key3' => 'item3',
            )
        );
        $multiple = new multiple('elementName', $parameters, $this->form);
        $this->assertEquals($expected, $multiple->__toString());
    }

    public function testSelectOptgroups() {
        $expected = '<p id="formName-elementName" class="input-multiple" data-errorMessage="Please enter valid data!">' .
            '<label>' .
                '<span class="label">elementName</span>' .
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

        $parameters = array(
            'skin' => 'select',
            'list' => array(
                'group1' => array(
                    'key1' => 'item1',
                    'key2' => 'item2',
                ),
                'group2' => array(
                    'key3' => 'item3',
                ),
                'key4' => 'item4',
            )
        );
        $multiple = new multiple('elementName', $parameters, $this->form);
        $this->assertEquals($expected, $multiple->__toString());
    }

    public function testCheckboxHtmlEscaping() {
        $expected = '<p id="formName-elementName" class="input-multiple required" title="ti&quot;&gt;tle" data-errorMessage="er&quot;&gt;rorMessage">' .
            '<span class="label">la&quot;&gt;bel <em>ma&quot;&gt;rker</em></span>' .
            '<span></span>' .
        '</p>' . "\n";

        $parameters = array(
            'label'         => 'la">bel',
            'marker'        => 'ma">rker',
            'errorMessage'  => 'er">rorMessage',
            'title'         => 'ti">tle',
            'required'      => true,
        );
        $multiple = new multiple('elementName', $parameters, $this->form);
        $this->assertEquals($expected, $multiple->__toString());
    }

    public function testSelectHtmlEscaping() {
        $expected = '<p id="formName-elementName" class="input-multiple required" title="ti&quot;&gt;tle" data-errorMessage="er&quot;&gt;rorMessage">' .
            '<label>' .
                '<span class="label">la&quot;&gt;bel <em>ma&quot;&gt;rker</em></span>' .
                '<select multiple name="elementName[]"></select>' .
            '</label>' .
        '</p>' . "\n";

        $parameters = array(
            'label'         => 'la">bel',
            'marker'        => 'ma">rker',
            'errorMessage'  => 'er">rorMessage',
            'title'         => 'ti">tle',
            'skin'          => 'select',
            'required'      => true,
        );
        $multiple = new multiple('elementName', $parameters, $this->form);
        $this->assertEquals($expected, $multiple->__toString());
    }

    public function testHtmlCheckboxEscapedList() {
        $expected = '<p id="formName-elementName" class="input-multiple" data-errorMessage="Please enter valid data!">' .
            '<span class="label">elementName</span>' .
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

        $parameters = array(
            'list' => array('it">em1', 'it">em2', 'it">em3'),
        );
        $multiple = new multiple('elementName', $parameters, $this->form);
        $this->assertEquals($expected, $multiple->__toString());
    }

    public function testHtmlSelectEscapedList() {
        $expected = '<p id="formName-elementName" class="input-multiple" data-errorMessage="Please enter valid data!">' .
            '<label>' .
                '<span class="label">elementName</span>' .
                '<select multiple name="elementName[]">' .
                    '<option value="0">it&quot;&gt;em1</option>' .
                    '<option value="1">it&quot;&gt;em2</option>' .
                    '<option value="2">it&quot;&gt;em3</option>' .
                '</select>' .
            '</label>' .
        '</p>' . "\n";

        $parameters = array(
            'list' => array('it">em1', 'it">em2', 'it">em3'),
            'skin' => 'select',
        );
        $multiple = new multiple('elementName', $parameters, $this->form);
        $this->assertEquals($expected, $multiple->__toString());
    }

     public function testCheckboxEscapedAssociativeList() {
        $expected = '<p id="formName-elementName" class="input-multiple" data-errorMessage="Please enter valid data!">' .
            '<span class="label">elementName</span>' .
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
        '</p>'. "\n";

        $parameters = array(
            'list' => array(
                'ke">y1' => 'it">em1',
                'ke">y2' => 'it">em2',
                'ke">y3' => 'it">em3',
            )
        );

        $multiple = new multiple('elementName', $parameters, $this->form);
        $this->assertEquals($expected, $multiple->__toString());
    }

    public function testSelectEscapedAssociativeList() {
        $expected = '<p id="formName-elementName" class="input-multiple" data-errorMessage="Please enter valid data!">' .
            '<label>' .
                '<span class="label">elementName</span>' .
                '<select multiple name="elementName[]">' .
                    '<option value="ke&quot;&gt;y1">it&quot;&gt;em1</option>' .
                    '<option value="ke&quot;&gt;y2">it&quot;&gt;em2</option>' .
                    '<option value="ke&quot;&gt;y3">it&quot;&gt;em3</option>' .
                '</select>' .
            '</label>' .
        '</p>' . "\n";

        $parameters = array(
            'skin' => 'select',
            'list' => array(
                'ke">y1' => 'it">em1',
                'ke">y2' => 'it">em2',
                'ke">y3' => 'it">em3',
            ),
        );

        $multiple = new multiple('elementName', $parameters, $this->form);
        $this->assertEquals($expected, $multiple->__toString());
    }

}
