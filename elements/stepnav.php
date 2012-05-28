<?php 
/**
 * @file    stepnav.php
 * @brief   Navigation element for steps
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 **/

namespace depage\htmlform\elements;

/**
 * @brief Can be used to insert a step navigation
 *
 * @section usage
 **/
class stepnav  {
    // {{{ variables
    /**
     * @brief HTML code to be printed
     **/
    private $htmlString;
    // }}}

    // {{{ __construct()
    /**
     * @brief   stepnav class constructor
     *
     * @param   $parameters (array)     input element parameters, HTML attributes, validator specs etc.
     * @param   $form       (object)    parent form object
     * @return  void
     **/
    public function __construct($parameters, $form) {
        $this->form = $form;
    }
    // }}}

    // {{{ __toString()
    /**
     * @brief   Renders element to HTML.
     *
     * @return  $htmlString (string) HTML-rendered element
     **/
    public function __toString() {
        $currentStepId = $this->form->getCurrentStepId();
        $firstInvalidStep = $this->form->getFirstInvalidStep();

        $htmlString = "<ol class=\"stepnav\">";
        foreach ($this->form->getSteps() as $stepNum => $step) {
            $link = "";
            $class = "step-$stepNum";
            $label = $step->htmlLabel();

            // add link to previously unsaved steps
            if ($stepNum <= $firstInvalidStep && $stepNum != $currentStepId) {
                $link = "href=\"{$this->form->url['path']}?step=$stepNum\"";
            }

            // add valid-class to previous steps
            if ($stepNum < $currentStepId) {
                $class .= $step->validate() ? " valid" : " invalid";
            }

            if ($stepNum == $currentStepId) {
                $htmlString .= "<li class=\"current-step $class\"><a $link><strong>$label</strong></a></li>";
            } else {
                $htmlString .= "<li class=\"$class\"><a $link>$label</a></li>";
            }
        }
        $htmlString .= "</ol>";

        return $htmlString;
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
