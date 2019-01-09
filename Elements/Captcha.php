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

/**
 * @brief Captcha
 * Class Captcha
 */
class Captcha extends Text
{
    /**
     * @brief captchaBuilder
     **/
    protected $captcha = null;

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
        parent::__construct($name, $parameters, $form);

        // @todo when to reuse old phrase?
        $this->captcha = new CaptchaBuilder();
        $this->phrase = $this->captcha
            ->setTextColor(0, 92, 132)
            ->setBackgroundColor(242, 238, 239)
            ->build(250, 100)
            ->getPhrase();
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
        } elseif ($newValue === "true") {
            $this->value = true;
        } else {
            $this->value = false;
        }

        return $this->value;
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

            $this->valid = (($this->value !== null)
                && ($this->validator->validate($this->value) || $this->isEmpty())
                && ($this->value || !$this->required)
            );
        }

        return $this->valid;
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

        return "<p {$wrapperAttributes}>" .
            $this->phrase .
            "<span class=\"captcha-img\">" .
                "<img src=\"" . $this->captcha->inline() . "\" />" .
            "</span>" .
            "<label>" .
                "<span class=\"depage-label\">{$label}{$marker}</span>" .
                "<input name=\"{$this->name}\" type=\"{$type}\"{$inputAttributes} value=\"{$value}\">" .
                $list .
            "</label>" .
            $errorMessage .
            $helpMessage .
        "</p>\n";
    }
    // }}}
}



// vim:set ft=php sw=4 sts=4 fdm=marker et :
