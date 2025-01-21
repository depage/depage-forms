<?php

use PHPUnit\Framework\TestCase;
use Depage\HtmlForm\HtmlForm;
use Depage\HtmlForm\Elements\Step;

/**
 * Tests for step container element rendering.
 **/
class stepToStringTest extends TestCase
{
    protected $form;
    protected $step;

    // {{{ setUp()
    public function setUp(): void
    {
        $this->form = new nameTestForm();
        $this->step = new Step('stepName', [], $this->form);
    }
    // }}}

    // {{{ testSimple()
    /**
     * Element with default setup (empty)
     **/
    public function testSimple()
    {
        $expected = '';
        $this->assertEquals($expected, $this->step->__toString());
    }
    // }}}

    // {{{ testAddFieldset()
    /**
     * With fieldset subelement.
     **/
    public function testAddFieldset()
    {
        $expected = '<fieldset id="formName-fieldsetName" name="fieldsetName">' .
            '<legend><span>fieldsetName</span></legend>' .
        '</fieldset>' . "\n";
        $this->step->addFieldset('fieldsetName');

        $this->assertEquals($expected, $this->step->__toString());
    }
    // }}}

    // {{{ testAddText()
    /**
     * With text subelement
     **/
    public function testAddText()
    {
        $expected = '<p id="formName-textName" class="input-text" data-errorMessage="Please enter valid data">' .
                '<label>' .
                    '<span class="depage-label">textName</span>' .
                    '<input name="textName" type="text" value="">' .
                '</label>' .
            '</p>' . "\n";

        $this->step->addText('textName');
        $this->assertEquals($expected, $this->step->__toString());
    }
    // }}}

    // {{{ testAddStepNav1()
    /**
     * With stepnav subelement
     **/
    public function testAddStepNav1()
    {
        $_GET['step'] = 0;
        $expected = '<ol class="stepnav"><li class="current-step step-0"><a ><strong>Step 1</strong></a></li><li class="step-1"><a href="http://www.depagecms.net/?step=1">Step 2</a></li></ol>';

        $form = new HtmlForm('formName');

        $step1 = $form->addStep('step1', ['label' => 'Step 1']);
        $step1->addStepNav();

        $step2 = $form->addStep('step2', ['label' => 'Step 2']);

        $this->assertEquals($expected, $step1->__toString());
    }
    // }}}

    // {{{ testAddStepNav1()
    /**
     * With stepnav subelement
     **/
    public function testAddStepNav2()
    {
        $_GET['step'] = 1;
        $expected = '<ol class="stepnav"><li class="step-0 valid"><a href="http://www.depagecms.net/?step=0">Step 1</a></li><li class="current-step step-1"><a ><strong>Step 2</strong></a></li></ol>';

        $form = new HtmlForm('formName');

        $step1 = $form->addStep('step1', ['label' => 'Step 1']);
        $step1->addStepNav();

        $step2 = $form->addStep('step2', ['label' => 'Step 2']);

        $this->assertEquals($expected, $step1->__toString());
    }
    // }}}
}
