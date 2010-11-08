<?php 

require_once('text.php');
require_once('email.php');
require_once('hidden.php');
require_once('url.php');
require_once('tel.php');
require_once('inputClass.php');
require_once('textarea.php');
require_once('password.php');
require_once('range.php');
require_once('number.php');

/**
 * The abstract class textClass is parent to text based input element classes.
 * It contains basic HTML rendering functionality.
 **/
abstract class textClass extends inputClass {
    /**
     * Renders element to HTML.
     *
     * @return string of HTML rendered element
     **/
    public function __toString() {
        $classes = $this->getClasses();
        $requiredChar = $this->getRequiredChar();
        return "<p id=\"$this->formName-$this->name\" class=\"$classes\"><label><span class=\"label\">$this->label<em>$requiredChar</em></span><input name=\"$this->name\" type=\"$this->type\" value=\"$this->value\"></label></p>\n";
    }

    /**
     * Overrides parent::setDefaults()
     *
     * return void
     **/
    protected function setDefaults() {
        parent::setDefaults();
        
        // textClass elements have values of type string
        $this->defaults['value'] = '';
    }
}
