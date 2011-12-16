<?php
/**
 * @file    hidden.php
 * @brief   hidden input element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace depage\htmlform\elements;

/**
 * @brief HTML hidden input type.
 *
 * Class for the HTML input-type "hidden".
 *
 * @section usage
 *
 * @code
 * <?php
 *     $form = new depage\htmlform\htmlform('myform');
 *
 *     // add a hidden field
 *     $form->addHidden('nonce', array(
 *         'defaultValue' => 'XD5fkk',
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
class hidden extends text {
    // {{{ __toString()
    /**
     * @brief   Renders element to HTML.
     *
     * @return  (string) HTML-rendered element
     **/
    public function __toString() {
        $formName   = $this->htmlFormName();
        $classes    = $this->htmlClasses();
        $value      = $this->htmlValue();

        return "<input name=\"{$this->name}\" id=\"{$formName}-{$this->name}\" type=\"{$this->type}\" class=\"{$classes}\" value=\"{$value}\">\n";
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
