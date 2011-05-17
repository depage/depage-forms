<?php
/**
 * @file    step.php
 * @brief   step container element
 **/

namespace depage\htmlform\elements;

/**
 * @brief Steps break up forms into separate consecutive parts
 **/
class step extends fieldset {
    // {{{ __toString()
    /**
     * @brief renders step container to HTML
     *
     * If the step contains elements it calls their rendering methods.
     * (unlike fieldsets, steps themselves aren't rendered)
     *
     * @return $renderedElement (string) HTML rendered element
     **/
     public function __toString() {
        $renderedElements = '';
        foreach($this->elementsAndHtml as $element) {
            $renderedElements .= $element;
        }
        return $renderedElements;
    }
    // }}}
}
