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
