<?php
/**
 * @file    richtext.php
 * @brief   richtext input element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace depage\htmlform\elements;

/**
 * @brief HTML richtext element.
 *
 * adds a textarea-element with additional richtext-functionality 
 * added by (optional) javascript
 *
 * @section usage
 *
 * @code
 * <?php
 *     $form = new depage\htmlform\htmlform('myform');
 *
 *     // add a richtext field
 *     $form->addRichtext('html', array(
 *         'label' => 'Richtext box',
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
class richtext extends textarea {
}

/* vim:set ft=php fenc=UTF-8 sw=4 sts=4 fdm=marker et : */
