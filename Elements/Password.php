<?php

/**
 * @file    password.php
 * @brief   password input element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace Depage\HtmlForm\Elements;

/**
 * @brief HTML password input type.
 *
 * Class for the HTML input type "password". Entered characters are masked with
 * asterisks or bullets (depends on browser).
 *
 * Usage
 * -----
 *
 * @code
    <?php
        $form = new Depage\HtmlForm\HtmlForm('myform');

        // add a password field
        $form->addPassword('userPass', array(
            'label' => 'Password',
        ));

        // process form
        $form->process();

        // Display the form.
        echo ($form);
    @endcode
 **/
class Password extends Text {}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
