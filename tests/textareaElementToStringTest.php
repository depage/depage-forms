<?php

require_once('../abstracts/input.php');
require_once('../elements/textarea.php');

use depage\htmlform\elements\textarea;

class textareaElementToStringTest extends PHPUnit_Framework_TestCase {
    public function testSimple() {
        $expected = '<p id="formName-elementName" class="input-textarea" data-errorMessage="Please enter valid data!">' .
            '<label>' .
                '<span class="label">elementName</span>' .
                '<textarea name="elementName"></textarea>' .
            '</label>' .
        '</p>' . "\n";

        $textarea = new textarea('elementName', $ref = array(), 'formName');
        $this->assertEquals($expected, $textarea->__toString());
    }

    public function testValue() {
        $expected = '<p id="formName-elementName" class="input-textarea" data-errorMessage="Please enter valid data!">' .
            '<label>' .
                '<span class="label">elementName</span>' .
                '<textarea name="elementName">testValue</textarea>' .
            '</label>' .
        '</p>' . "\n";

        $textarea = new textarea('elementName', $ref = array(), 'formName');
        $textarea->setValue('testValue');
        $this->assertEquals($expected, $textarea->__toString());
    }

    public function testRequired() {
        $expected = '<p id="formName-elementName" class="input-textarea required" data-errorMessage="Please enter valid data!">' .
            '<label>' .
                '<span class="label">elementName <em>*</em></span>' .
                '<textarea name="elementName" required></textarea>' .
            '</label>' .
        '</p>' . "\n";

        $textarea = new textarea('elementName', $ref = array(), 'formName');
        $textarea->setRequired();
        $this->assertEquals($expected, $textarea->__toString());
    }

    public function testHtmlEscaping() {
        $expected = '<p id="formName-elementName" class="input-textarea" title="ti&quot;&gt;tle" data-errorMessage="er&quot;&gt;rorMessage">' .
            '<label>' .
                '<span class="label">la&quot;&gt;bel</span>' .
                '<textarea name="elementName"></textarea>' .
            '</label>' .
        '</p>' . "\n";

        $parameters = array(
            'label'         => 'la">bel',
            'marker'        => 'ma">rker',
            'errorMessage'  => 'er">rorMessage',
            'title'         => 'ti">tle',
        );
        $textarea = new textarea('elementName', $parameters, 'formName');
        $this->assertEquals($expected, $textarea->__toString());
    }
}
