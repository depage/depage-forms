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
    protected function setDefaults()
    {
        parent::setDefaults();

        $this->defaults['rows'] = null;
        $this->defaults['cols'] = null;
        $this->defaults['stylesheet'] = null;
        $this->defaults['autogrow'] = true;
        $this->defaults['allowedTags'] = array(
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
        );
    }
    // }}}

    // {{{ htmlDataAttributes()
    /**
     * @brief   Returns dataAttr escaped as attribute string
     **/
    protected function htmlDataAttributes()
    {
        $options = array();
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
    public function isEmpty()
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
    protected function htmlValue()
    {
        if ($this->value === null) {
            $htmlDOM = $this->parseHtml($this->defaultValue);
        } else {
            $htmlDOM = $this->value;
        }

        $html = "";

        // add content of every node in body
        foreach ($htmlDOM->documentElement->childNodes as $node) {
            $html .= $htmlDOM->saveXML($node);
        }

        return $this->htmlEscape($html);
    }
    // }}}
    // {{{ typeCastValue()
    /**
     * @brief   Converts value into htmlDOM
     *
     * @return void
     **/
    protected function typeCastValue()
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
    protected function parseHtml($html)
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
