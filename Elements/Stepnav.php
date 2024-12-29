<?php

/**
 * @file    stepnav.php
 * @brief   Navigation element for steps
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 **/

namespace Depage\HtmlForm\Elements;

/**
 * @brief Can be used to insert a step navigation
 *
 * Usage
 * -----
 **/
class Stepnav
{
    // {{{ variables
    /**
     * @brief HTML code to be printed
     **/
    private $htmlString;

    /**
     * @brief form object
     **/
    protected $form;
    // }}}

    // {{{ __construct()
    /**
     * @brief   stepnav class constructor
     *
     * @param  array  $parameters input element parameters, HTML attributes, validator specs etc.
     * @param  object $form       parent form object
     **/
    public function __construct(array $parameters, object $form)
    {
        $this->form = $form;
    }
    // }}}

    // {{{ __toString()
    /**
     * @brief   Renders element to HTML.
     *
     * @return string $htmlString HTML-rendered element
     **/
    public function __toString(): string
    {
        $scheme   = isset($this->form->url['scheme']) ? $this->form->url['scheme'] . '://' : '';
        $host     = isset($this->form->url['host']) ? $this->form->url['host'] : '';
        $port     = isset($this->form->url['port']) ? ':' . $this->form->url['port'] : '';
        $path     = isset($this->form->url['path']) ? $this->form->url['path'] : '';
        $baseUrl  = "$scheme$host$port$path";


        $currentStepId = $this->form->getCurrentStepId();
        $firstInvalidStep = $this->form->getFirstInvalidStep();

        $htmlString = "<ol class=\"stepnav\">";
        foreach ($this->form->getSteps() as $stepNum => $step) {
            $link = "";
            $class = "step-$stepNum";
            $label = $step->htmlLabel();

            // add link to previously unsaved steps
            if ($stepNum <= $firstInvalidStep && $stepNum != $currentStepId) {
                $link = "href=\"" . htmlspecialchars($baseUrl . $this->form->buildUrlQuery(['step' => $stepNum])) . "\"";
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
