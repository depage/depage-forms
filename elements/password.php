<?php
/**
 * @file    password.php
 * @brief   password input element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace depage\htmlform\elements;

/** 
 * @brief HTML password input type.
 *
 * Class for the HTML input type "password". Entered characters are masked with
 * asterisks or bullets (depends on browser).
 *
 * @section usage
 *
 * @code
 * <?php
 *     $form = new depage\htmlform\htmlform('myform');
 *
 *     // add a password field
 *     $form->addPassword('userPass', array(
 *         'label' => 'Password',
 *     ));
 *
 *     // process form
 *     $form->process();
 *
 *     // Display the form.
 *     echo ($form);
 * ?>
 * @endcode
 **/
class password extends text {
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
