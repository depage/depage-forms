<?php
/**
 * @file    elements/email.php
 * @brief   email input element
 **/

namespace depage\htmlform\elements;

/**
 * @brief HTML email input type.
 *
 * Class for the HTML5 input-type "email".
 * 
 * @section usage
 *
 * @code
 * <?php 
 *     $form = new depage\htmlform\htmlform('myform');
 *
 *     // add a required email field
 *     $form->addEmail('email', array(
 *         'label' => 'Email address',
 *         'required' => true,
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
class email extends text {
    // {{{ setDefaults()
    /**
     * @brief collects initial values across subclasses.
     **/
    protected function setDefaults() {
        parent::setDefaults();
        $this->defaults['errorMessage'] = 'Please enter a valid e-mail address!';
    }
    // }}}
}
