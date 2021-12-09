<?php

use PHPUnit\Framework\TestCase;
use Depage\HtmlForm\HtmlForm;

/**
 * Tests rendering the form.
 **/
class htmlformToStringTest extends TestCase
{
    // {{{ testSimple()
    /**
     * Form with default setup
     **/
    public function testSimple()
    {
        $expected = '<form id="formName" name="formName" class="depage-form " method="post" action="http://www.depagecms.net/" data-jsvalidation="blur" data-jsautosave="false" enctype="multipart/form-data">' . "\n" .
            '<input name="formName" id="formName-formName" type="hidden" class="input-hidden" value="formName">' . "\n" .
            '<input name="formStep" id="formName-formStep" type="hidden" class="input-hidden" value="0">' . "\n" .
            '<input name="formCsrfToken" id="formName-formCsrfToken" type="hidden" class="input-hidden" value="xxxxxxxx">' . "\n" .
            '<p id="formName-submit" class="submit">' .
                '<input type="submit" name="formSubmit" value="submit">' .
            '</p>' . "\n" .
        '</form>';

        $form = new csrfTestForm('formName');
        $this->assertEquals($expected, $form->__toString());
    }
    // }}}

    // {{{ testCancel()
    /**
     * Form with default setup
     **/
    public function testCancel()
    {
        $expected = '<form id="formName" name="formName" class="depage-form " method="post" action="http://www.depagecms.net/" data-jsvalidation="blur" data-jsautosave="false" enctype="multipart/form-data">' . "\n" .
            '<input name="formName" id="formName-formName" type="hidden" class="input-hidden" value="formName">' . "\n" .
            '<input name="formStep" id="formName-formStep" type="hidden" class="input-hidden" value="0">' . "\n" .
            '<input name="formCsrfToken" id="formName-formCsrfToken" type="hidden" class="input-hidden" value="xxxxxxxx">' . "\n" .
            '<p id="formName-submit" class="submit">' .
                '<input type="submit" name="formSubmit" value="submit">' .
            '</p>' . "\n" .
            '<p id="formName-cancel" class="cancel">' .
                '<input type="submit" name="formSubmit" value="cancel">' .
            '</p>' . "\n" .
        '</form>';

        $form = new csrfTestForm('formName', array(
            'cancelLabel' => "cancel",
        ));
        $this->assertEquals($expected, $form->__toString());
    }
    // }}}

    // {{{ testDisabled()
    /**
     * Form with default setup
     **/
    public function testDisabled()
    {
        $expected = '<form id="formName" name="formName" class="depage-form " method="post" action="http://www.depagecms.net/" data-jsvalidation="blur" data-jsautosave="false" enctype="multipart/form-data">' . "\n" .
            '<input name="formName" id="formName-formName" type="hidden" class="input-hidden" value="formName">' . "\n" .
            '<input name="formStep" id="formName-formStep" type="hidden" class="input-hidden" value="0">' . "\n" .
            '<input name="formCsrfToken" id="formName-formCsrfToken" type="hidden" class="input-hidden" value="xxxxxxxx">' . "\n" .
            '<p id="formName-submit" class="submit">' .
                '<input type="submit" name="formSubmit" value="submit" disabled="disabled">' .
            '</p>' . "\n" .
        '</form>';

        $form = new csrfTestForm('formName', array(
            'disabled' => true,
        ));
        $this->assertEquals($expected, $form->__toString());
    }
    // }}}

    // {{{ testStep()
    /**
     * Form with 2 steps, only step1 should be rendered.
     **/
    public function testStep()
    {
        $expected = '<form id="formName" name="formName" class="depage-form " method="post" action="http://www.depagecms.net/?step=1" data-jsvalidation="blur" data-jsautosave="false" enctype="multipart/form-data">' . "\n" .
            '<input name="formName" id="formName-formName" type="hidden" class="input-hidden" value="formName">' . "\n" .
            '<input name="formStep" id="formName-formStep" type="hidden" class="input-hidden" value="1">' . "\n" .
            '<input name="formCsrfToken" id="formName-formCsrfToken" type="hidden" class="input-hidden" value="xxxxxxxx">' . "\n" .
            '<p id="formName-text1" class="input-text" data-errorMessage="Please enter valid data">' .
                '<label>' .
                    '<span class="depage-label">text1</span>' .
                    '<input name="text1" type="text" value="">' .
                '</label>' .
            '</p>' . "\n" .
            '<p id="formName-submit" class="submit">' .
                '<input type="submit" name="formSubmit" value="submit">' .
            '</p>' . "\n" .
        '</form>';

        $_GET['step'] = 1;
        $form = new csrfTestForm('formName');
        $step0 = $form->addStep('step0');
        $step0->addText('text0');
        $step1 = $form->addStep('step1');
        $step1->addText('text1');

        $this->assertEquals($expected, $form->__toString());
    }
    // }}}

    // {{{ testStepBack()
    /**
     * Form with 2 steps, only step1 should be rendered.
     **/
    public function testStepBack()
    {
        $expected = '<form id="formName" name="formName" class="depage-form " method="post" action="http://www.depagecms.net/?step=1" data-jsvalidation="blur" data-jsautosave="false" enctype="multipart/form-data">' . "\n" .
            '<input name="formName" id="formName-formName" type="hidden" class="input-hidden" value="formName">' . "\n" .
            '<input name="formStep" id="formName-formStep" type="hidden" class="input-hidden" value="1">' . "\n" .
            '<input name="formCsrfToken" id="formName-formCsrfToken" type="hidden" class="input-hidden" value="xxxxxxxx">' . "\n" .
            '<p id="formName-text1" class="input-text" data-errorMessage="Please enter valid data">' .
                '<label>' .
                    '<span class="depage-label">text1</span>' .
                    '<input name="text1" type="text" value="">' .
                '</label>' .
            '</p>' . "\n" .
            '<p id="formName-submit" class="submit">' .
                '<input type="submit" name="formSubmit" value="submit">' .
            '</p>' . "\n" .
            '<p id="formName-back" class="back">' .
                '<input type="submit" name="formSubmit" value="back">' .
            '</p>' . "\n" .
        '</form>';

        $_GET['step'] = 1;
        $form = new csrfTestForm('formName', array(
            'backLabel' => "back",
        ));
        $step0 = $form->addStep('step0');
        $step0->addText('text0');
        $step1 = $form->addStep('step1');
        $step1->addText('text1');

        $this->assertEquals($expected, $form->__toString());
    }
    // }}}
}
