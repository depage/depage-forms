<?php
/**
 * @file    Captcha.php
 *
 * description
 *
 * copyright (c) 2019 Frank Hellenkamp [jonas@depage.net]
 *
 * @author    Frank Hellenkamp [jonas@depage.net]
 */

namespace Depage\HtmlForm\Elements;

use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;

/**
 * @brief Captcha
 * Class Captcha
 */
class Captcha extends Text
{
    /**
     * @brief captcha
     **/
    protected $captcha = null;

    /**
     * @brief sessionSlot
     **/
    protected $sessionSlot = null;

    // {{{ setDefaults()
    /**
     * @brief   collects initial values across subclasses.
     *
     * The constructor loops through these and creates settable class
     * attributes at runtime. It's a compact mechanism for initialising
     * a lot of variables.
     *
     * @return void
     **/
    protected function setDefaults()
    {
        parent::setDefaults();
        $this->defaults['defaultValue'] = false;
        $this->defaults['errorMessage'] = _('Please fill in the correct phrase.');
        $this->defaults['textColor'] = false;
        $this->defaults['backgroundColor'] = false;
        $this->defaults['width'] = 250;
        $this->defaults['height'] = 100;
    }
    // }}}
    // {{{ __construct()
    /**
     * @brief __construct
     *
     * @param  string $name       element name
     * @param  array  $parameters element parameters, HTML attributes, validator specs etc.
     * @param  object $form       parent form object
     * @return void
     **/
    public function __construct($name, $parameters, $form)
    {
        $this->captcha = new CaptchaBuilder();

        parent::__construct($name, $parameters, $form);

        if (is_array($this->textColor)) {
            $this->captcha->setTextColor($this->textColor[0], $this->textColor[1], $this->textColor[2]);
        }
        if (is_array($this->backgroundColor)) {
            $this->captcha->setBackgroundColor($this->backgroundColor[0], $this->backgroundColor[1], $this->backgroundColor[2]);
        }
    }
    // }}}
    // {{{ setValue()
    /**
     * @brief   set the boolean element value
     *
     * Sets the current input elements' value. Converts it to boolean if
     * necessary.
     *
     * @param  mixed $newValue new element value
     * @return bool  $this->value    converted value
     **/
    public function setValue($newValue)
    {
        if (is_bool($newValue)) {
            $this->value = $newValue;
        } else {
            $this->value = $newValue;
        }

        return $this->value;
    }
    // }}}
    // {{{ setSessionSlot()
    /**
     * @brief setSessionSlot
     *
     * @param mixed &$
     * @return void
     **/
    public function setSessionSlot(&$sessionSlot)
    {
        $this->sessionSlot = &$sessionSlot;

    }
    // }}}
    // {{{ validate()
    /**
     * @brief validates boolean input element value
     *
     * Overrides input::validate(). Checks if the value of the current input
     * element is valid according to it's validator object. In case of boolean
     * the value has to be true if field is required.
     *
     * @return bool $this->valid validation result
     **/
    public function validate()
    {
        if (!$this->validated) {
            $this->validated = true;

            $this->valid = $this->value === true || $this->testPhrase($this->value);
        }

        return $this->valid;
    }
    // }}}
    // {{{ testPhrase()
    /**
     * @brief testPhrase
     *
     * @param mixed
     * @return void
     **/
    protected function testPhrase($value)
    {
        $this->captcha->setPhrase($this->sessionSlot['formCaptcha']);

        if (!$this->captcha->testPhrase($value)) {
            $this->getNewPhrase();

            return false;
        }

        return true;
    }
    // }}}
    // {{{ getNewPhrase()
    /**
     * @brief getNewPhrase
     *
     * @return void
     **/
    protected function getNewPhrase()
    {
        $phraseBuilder = new PhraseBuilder();
        $phrase = $phraseBuilder->build();

        $this->captcha->setPhrase($phrase);
        $this->sessionSlot['formCaptcha'] = $phrase;
    }
    // }}}
    // {{{ __toString()
    /**
     * @brief   Renders element to HTML.
     *
     * @return string HTML rendered element
     **/
    public function __toString()
    {
        $value              = $this->htmlValue();
        $type               = strtolower($this->type);
        $inputAttributes    = $this->htmlInputAttributes();
        $marker             = $this->htmlMarker();
        $label              = $this->htmlLabel();
        $list               = $this->htmlList();
        $wrapperAttributes  = $this->htmlWrapperAttributes();
        $errorMessage       = $this->htmlErrorMessage();
        $helpMessage        = $this->htmlHelpMessage();

        if (empty($this->sessionSlot['formCaptcha'])) {
            $this->getNewPhrase();
        }

        $this->captcha->build($this->width, $this->height);

        return "<p {$wrapperAttributes}>" .
            "<label>" .
                "<span class=\"depage-label\">{$label}{$marker}</span>" .
                "<span class=\"captcha-img\">" .
                    "<img src=\"" . $this->captcha->inline() . "\" />" .
                "</span>" .
                "<input name=\"{$this->name}\" type=\"{$type}\"{$inputAttributes} value=\"$value\">" .
                $list .
            "</label>" .
            $errorMessage .
            $helpMessage .
        "</p>\n";
    }
    // }}}
}



// vim:set ft=php sw=4 sts=4 fdm=marker et :
