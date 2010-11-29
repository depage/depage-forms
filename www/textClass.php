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
    public function render($value, $requiredAttribute, $requiredChar, $class) {
        return "<p id=\"$this->formName-$this->name\" class=\"$class\">" .
            "<label>" .
                "<span class=\"label\">$this->label$requiredChar</span>" .
                "<input name=\"$this->name\" type=\"$this->type\"$requiredAttribute value=\"$value\">" .
            "</label>" .
        "</p>\n";
    }

    /**
     * Overrides parent::setDefaults()
     *
     * return void
     **/
    protected function setDefaults() {
        parent::setDefaults();
        
        // textClass elements have values of type string
        $this->defaults['defaultValue'] = '';
    }
}
