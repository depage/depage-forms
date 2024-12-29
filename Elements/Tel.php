<?php

/**
 * @file    elements/tel.php
 * @brief   tel input element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace Depage\HtmlForm\Elements;

/**
 * @brief HTML tel input type.
 *
 * Class for the HTML5 input-type "tel".
 *
 * Usage
 * -----
 *
 * @code
    <?php
        $form = new Depage\HtmlForm\HtmlForm('myform');

        // add a required tel field
        $form->addTel('tel', array(
            'label' => 'Telephone number',
            'required' => true,
        ));

        // process form
        $form->process();

        // Display the form.
        echo ($form);
    @endcode
 **/
class Tel extends Text
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

        $this->defaults['errorMessage'] = _('Please enter a valid telephone number');
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
