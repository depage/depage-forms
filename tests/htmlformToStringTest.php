<?php

use depage\htmlform\htmlform;

/**
 * Tests rendering the form.
 **/
class htmlformToStringTest extends PHPUnit_Framework_TestCase {
    // {{{ testSimple()
    /**
     * Form with default setup
     **/
    public function testSimple() {
        $expected = '<form id="formName" name="formName" class="depage-form " method="post" action="http://www.depagecms.net/" data-jsvalidation="blur" data-jsautosave="false" enctype="multipart/form-data">' . "\n" .
            '<input name="formName" id="formName-formName" type="hidden" class="input-hidden" value="formName">' . "\n" .
            '<p id="formName-submit" class="submit">' .
                '<input type="submit" name="formSubmit" value="submit">' .
            '</p>' . "\n" .
        '</form>';

        $form = new htmlform('formName');
        $this->assertEquals($expected, $form->__toString());
    }
    // }}}

    // {{{ testCancel()
    /**
     * Form with default setup
     **/
    public function testCancel() {
        $expected = '<form id="formName" name="formName" class="depage-form " method="post" action="http://www.depagecms.net/" data-jsvalidation="blur" data-jsautosave="false" enctype="multipart/form-data">' . "\n" .
            '<input name="formName" id="formName-formName" type="hidden" class="input-hidden" value="formName">' . "\n" .
            '<p id="formName-submit" class="submit">' .
                '<input type="submit" name="formSubmit" value="submit">' .
            '</p>' . "\n" .
            '<p id="formName-cancel" class="cancel">' .
                '<input type="submit" name="formSubmit" value="cancel">' .
            '</p>' . "\n" .
        '</form>';

        $form = new htmlform('formName', array(
            'cancelLabel' => "cancel",
        ));
        $this->assertEquals($expected, $form->__toString());
    }
    // }}}

    // {{{ testStep()
    /**
     * Form with 2 steps, only step1 should be rendered.
     **/
    public function testStep() {
        $expected = '<form id="formName" name="formName" class="depage-form " method="post" action="http://www.depagecms.net/" data-jsvalidation="blur" data-jsautosave="false" enctype="multipart/form-data">' . "\n" .
            '<input name="formName" id="formName-formName" type="hidden" class="input-hidden" value="formName">' . "\n" .
            '<p id="formName-text1" class="input-text" data-errorMessage="Please enter valid data">' .
                '<label>' .
                    '<span class="label">text1</span>' .
                    '<input name="text1" type="text" value="">' .
                '</label>' .
            '</p>' . "\n" .
            '<p id="formName-submit" class="submit">' .
                '<input type="submit" name="formSubmit" value="submit">' .
            '</p>' . "\n" .
        '</form>';

        $_GET['step'] = 1;
        $form = new htmlform('formName');
        $step0 = $form->addStep('step0');
        $step0->addText('text0');
        $step1 = $form->addStep('step1');
        $step1->addText('text1');

        $this->assertEquals($expected, $form->__toString());
    }
    // }}}
}
