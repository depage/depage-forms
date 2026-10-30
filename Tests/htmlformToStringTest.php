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

        $form = new csrfTestForm('formName', [
            'cancelLabel' => "cancel",
        ]);
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

        $form = new csrfTestForm('formName', [
            'disabled' => true,
        ]);
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
            '<p id="formName-text1" class="input-text" data-errormessage="Please enter valid data">' .
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
            '<p id="formName-text1" class="input-text" data-errormessage="Please enter valid data">' .
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
        $form = new csrfTestForm('formName', [
            'backLabel' => "back",
        ]);
        $step0 = $form->addStep('step0');
        $step0->addText('text0');
        $step1 = $form->addStep('step1');
        $step1->addText('text1');

        $this->assertEquals($expected, $form->__toString());
    }
    // }}}

    // {{{ testCustomHtml()
    /**
     * Form with custom HTML: addHtml is printed as-is while
     * addSanitizedHtml is cleaned up before rendering.
     **/
    public function testCustomHtml()
    {
        $expected = '<form id="formName" name="formName" class="depage-form " method="post" action="http://www.depagecms.net/" data-jsvalidation="blur" data-jsautosave="false" enctype="multipart/form-data">' . "\n" .
            '<input name="formName" id="formName-formName" type="hidden" class="input-hidden" value="formName">' . "\n" .
            '<input name="formStep" id="formName-formStep" type="hidden" class="input-hidden" value="0">' . "\n" .
            '<input name="formCsrfToken" id="formName-formCsrfToken" type="hidden" class="input-hidden" value="xxxxxxxx">' . "\n" .
            '<div id="myimage">raw</div>' .
            '<p>sanitized</p>' . "\n" .
            '<p>keep</p>' .
            '<p id="formName-submit" class="submit">' .
                '<input type="submit" name="formSubmit" value="submit">' .
            '</p>' . "\n" .
        '</form>';

        // step-parameter is set by previous tests
        unset($_GET['step']);

        $form = new csrfTestForm('formName');
        $form->addHtml('<div id="myimage">raw</div>');
        $form->addSanitizedHtml('<div>sanitized</div><p onclick="alert(1)">keep</p>');

        $this->assertEquals($expected, $form->__toString());
    }
    // }}}
}
