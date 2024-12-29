<?php

/**
 * @file    richtext.php
 * @brief   richtext input element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace Depage\HtmlForm\Elements;

/**
 * @brief HTML richtext element.
 *
 * adds a textarea-element with additional richtext-functionality
 * added by javascript
 *
 * Usage
 * -----
 *
 * @code
    <?php
        $form = new Depage\HtmlForm\HtmlForm('myform');

        // add a richtext field
        $form->addRichtext('html', array(
            'label' => 'Richtext box',
        ));

        // process form
        $form->process();

        // Display the form.
        echo ($form);
    @endcode
 **/
class Richtext extends Textarea
{
    // {{{ variables
    /**
     * @brief   stylesheet
     **/
    public $stylesheet = null;

    /**
     * @brief   allowedTags
     **/
    public $allowedTags = [];

    /**
     * @brief   autogrow
     **/
    public $autogrow = true;
    // }}}

    // {{{ setDefaults()
    /**
     * @brief   collects initial values across subclasses
     *
     * The constructor loops through these and creates settable class
     * attributes at runtime. It's a compact mechanism for initialising
     * a lot of variables.
     *
     * @return void
     **/
    protected function setDefaults(): void
    {
        parent::setDefaults();

        $this->defaults['rows'] = null;
        $this->defaults['cols'] = null;
        $this->defaults['stylesheet'] = null;
        $this->defaults['autogrow'] = true;
        $this->defaults['allowedTags'] = [
            // inline elements
            "a",
            "b",
            "strong",
            "i",
            "em",
            "small",

            // block elements
            "p",
            "br",
            "h1",
            "h2",
            "ul",
            "ol",
            "li",
        ];
    }
    // }}}

    // {{{ htmlDataAttributes()
    /**
     * @brief   Returns dataAttr escaped as attribute string
     **/
    protected function htmlDataAttributes(): string
    {
        $options = [];
        $options['stylesheet'] = $this->stylesheet;
        $options['allowedTags'] = $this->allowedTags;

        $this->dataAttr['richtext-options'] = json_encode($options);

        return parent::htmlDataAttributes();
    }
    // }}}

    // {{{ isEmpty()
    /**
     * @brief   says wether the element value is empty
     *
     * Checks wether the input element value is empty. Accepts '0' and false as
     * not empty.
     *
     * @return bool empty-check result
     **/
    public function isEmpty(): bool
    {
        return empty(trim(strip_tags($this->value)));
    }
    // }}}

    // {{{ htmlValue()
    /**
     * @brief   Returns HTML-rendered element value
     *
     * @return mixed element value
     **/
    protected function htmlValue(): string
    {
        if ($this->value === null) {
            $htmlDOM = $this->parseHtml($this->defaultValue);
        } else {
            $htmlDOM = $this->value;
        }

        $html = (string) $htmlDOM;

        return $this->htmlEscape($html);
    }
    // }}}
    // {{{ typeCastValue()
    /**
     * @brief   Converts value into htmlDOM
     *
     * @return void
     **/
    protected function typeCastValue(): void
    {
        if (is_string($this->value)) {
            $this->value = $this->parseHtml($this->value);
        }
    }
    // }}}
    // {{{ parseHtml()
    /**
     * @brief   Parses html-string into htmlDOM
     *
     * @param string $html html string to parse
     *
     * @return Depage::HtmlForm::Abstract::HtmlDom htmlDOM
     **/
    protected function parseHtml(string $html): \Depage\HtmlForm\Abstracts\HtmlDom
    {
        if ($this->normalize && class_exists("\\Normalizer")) {
            $html = \Normalizer::normalize($html);
        }

        $htmlDOM = new \Depage\HtmlForm\Abstracts\HtmlDom();

        $htmlDOM->loadHTML($html);
        $htmlDOM->cleanHTML($this->allowedTags);

        if ($this->maxlength) {
            $htmlDOM->cutToMaxlength($this->maxlength);
        }

        return $htmlDOM;
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
