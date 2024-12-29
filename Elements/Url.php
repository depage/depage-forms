<?php

/**
 * @file    elements/url.php
 * @brief   url input element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace Depage\HtmlForm\Elements;

/**
 * @brief HTML url input-type
 *
 * Class for HTML5 input-type "url".
 *
 * Usage
 * -----
 *
 * @code
    <?php
        $form = new Depage\HtmlForm\HtmlForm('myform');

        // add an URL field
        $form->addUrl('homepage', array(
            'label' => 'Homepage URL',
        ));

        // process form
        $form->process();

        // Display the form.
        echo ($form);
    @endcode
 **/
class Url extends Text
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
    protected function setDefaults(): void
    {
        parent::setDefaults();

        $this->defaults['errorMessage'] = _('Please enter a valid URL');

        // @todo add option to test url by domain name (dns) or also with a request (for http/https urls)
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
        parent::typeCastValue();

        if ($this->normalize) {
            $this->value = \Depage\HtmlForm\Validators\Url::normalizeUrl($this->value);
        }
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
