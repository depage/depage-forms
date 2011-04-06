<?php

use depage\htmlform\elements\textarea;

class textareaElementToStringTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $parameters     = array();
        $this->form     = new nameTestForm;
        $this->textarea = new textarea('textareaName', $parameters, $this->form);
    }
 
    public function testSimple() {
        $expected = '<p id="formName-textareaName" class="input-textarea" data-errorMessage="Please enter valid data!">' .
            '<label>' .
                '<span class="label">textareaName</span>' .
                '<textarea name="textareaName"></textarea>' .
            '</label>' .
        '</p>' . "\n";

        $this->assertEquals($expected, $this->textarea->__toString());
    }

    public function testValue() {
        $expected = '<p id="formName-textareaName" class="input-textarea" data-errorMessage="Please enter valid data!">' .
            '<label>' .
                '<span class="label">textareaName</span>' .
                '<textarea name="textareaName">testValue</textarea>' .
            '</label>' .
        '</p>' . "\n";

        $this->textarea->setValue('testValue');
        $this->assertEquals($expected, $this->textarea->__toString());
    }

    public function testRequired() {
        $expected = '<p id="formName-textareaName" class="input-textarea required" data-errorMessage="Please enter valid data!">' .
            '<label>' .
                '<span class="label">textareaName <em>*</em></span>' .
                '<textarea name="textareaName" required></textarea>' .
            '</label>' .
        '</p>' . "\n";

        $this->textarea->setRequired();
        $this->assertEquals($expected, $this->textarea->__toString());
    }

    public function testHtmlEscaping() {
        $expected = '<p id="formName-textareaName" class="input-textarea" title="ti&quot;&gt;tle" data-errorMessage="er&quot;&gt;rorMessage">' .
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
}
