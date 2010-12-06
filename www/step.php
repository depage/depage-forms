<?php

require_once('fieldset.php');

class step extends fieldset {
    /**
     * If the step contains elements it calls their rendering methods.
     * (unlike fieldsets, steps themselves aren't rendered)
     *
     * @return string
     **/
     public function __toString() {
        $renderedElements = '';
        foreach($this->elementsAndHtml as $element) {
            $renderedElements .= $element;
        }
        return $renderedElements;
    }
}
