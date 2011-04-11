<?php
/**
 * The abstract class inputClass holds the intersections of all implemented
 * HTML input element types. It handles validation, manual value manipulation
 * and constructor parameter checks.
 **/

namespace depage\htmlform\abstracts;

use depage\htmlform\validators;

abstract class input extends element {
    /**
     * Input element type - HTML input type attribute.
     **/
    protected $type;
    /**
     * Input element - HTML label
     **/
    protected $label;
    /**
     * True if the input element is required to hold a value to be valid.
     **/
    protected $required;
    /**
     * Name of the parent HTML form. Used to identify the element once it's rendered.
     **/
    protected $formName;
    /**
     * Input elements's value.
     **/
    protected $value = null;
    /**
     * Holds validator object reference.
     **/
    protected $validator;
    /**
     * HTML classes attribute for rendering the input element.
     **/
    protected $classes;
    /**
     * HTML autofocus attribute
     **/
    protected $autofocus = false;
    /**
     * HTML pattern attribute
     **/
    protected $pattern = false;

    /**
     * @param $name input elements' name
     * @param $parameters array of input element parameters, HTML attributes, validator specs etc.
     * @param $form parent form object.
     **/
    public function __construct($name, $parameters, $form) {
        $this->type         = strtolower(str_replace('depage\\htmlform\\elements\\', '', get_class($this)));
        $this->formName     = $form->getName();

        parent::__construct($name, $parameters, $form);

        $this->validator    = (isset($parameters['validator']))
            ? validators\validator::factory($parameters['validator'], $this->log)
            : validators\validator::factory($this->type, $this->log);
    }

    /**
     * collects initial values across subclasses.
     **/
    protected function setDefaults() {
        parent::setDefaults();

        $this->defaults['autofocus']    = false;
        $this->defaults['label']        = $this->name;
        $this->defaults['required']     = false;
        $this->defaults['marker']       = '*';
        $this->defaults['errorMessage'] = 'Please enter valid data!';
        $this->defaults['title']        = false;
    }

    /**
     * Checks if the value the current input element holds is valid according
     * to it's validator object.
     *
     * @return $this->valid
     **/
    public function validate() {
        if (!$this->validated) {
            $this->validated = true;

            $this->valid = (
                ($this->value !== null)
                && ($this->validatorCall() || $this->isEmpty())
                && (!$this->isEmpty() || !$this->required)
            );
        }
        return $this->valid;
    }

    /**
     * Hook method for validator call. Arguments can be adjusted on override.
     **/
    protected function validatorCall() {
        return $this->validator->validate($this->value);
    }

    /**
     * Checks wether the element-value is empty. Accepts '0' and false as not
     * empty.
     * 
     * @return bool
     **/
    protected function isEmpty() {
        return (
            empty($this->value)
            && ((string) $this->value !== '0') 
            && ($this->value !== false)
        );
    }

    /**
     * Allows to manually set the current input elements value.
     *
     * @param $newValue contains the new value
     * @return $this->value
     **/
    public function setValue($newValue) {
        $this->value = $newValue;
        $this->typeCastValue();
        return $this->value;
    }

    /**
     * Returns the current input elements' value.
     *
     * @return $this->value
     **/
    public function getValue() {
        return $this->value;
    }

    /**
     * Allows to manually set the current input elements value.
     *
     * @param $newValue contains the new value
     * @return $this->value
     **/
    public function setDefaultValue($newDefaultValue) {
        $this->defaultValue = $newDefaultValue;
    }

    /**
     * Converts value to element specific type. (to be overridden by element
     * child classes)
     **/
    protected function typeCastValue() {
    }

    /**
     * Sets the HTML autofocus attribute of the current input element.
     *
     * @return void
     **/
    public function setAutofocus($autofocus = true) {
        $this->autofocus = (bool) $autofocus;
    }

    /**
     * Sets the HTML required attribute of the current input element.
     *
     * @return void
     **/
    public function setRequired($required = true) {
        $this->required = (bool) $required;
    }

    /**
     * Returns a string of the HTML elements classes, separated by a space.
     *
     * @return $classes
     **/
    protected function htmlClasses() {
        $classes = 'input-' . htmlentities($this->type, ENT_QUOTES);
        
        if ($this->required) {
            $classes .= ' required';
        }
        if ($this->value !== null) {
            if (!$this->validate()) {
                $classes .= ' error';
            }
        }

        return $classes;
    }

    /**
     * Returns current input elements required - indicator.
     *
     * @return $this->marker or empty string
     **/
    protected function htmlMarker() {
        return ($this->required) ? " <em>" . htmlentities($this->marker, ENT_QUOTES) . "</em>" : "";
    }

    /**
     * Returns string of HTML attributes for input element.
     *
     * @return string HTML attribute
     **/
    protected function htmlInputAttributes() {
        $attributes = '';

        if ($this->required)    $attributes .= " required";
        if ($this->autofocus)   $attributes .= " autofocus";

        return $attributes;
    }

    /**
     * Returns string of HTML attributes for element wrapper paragraph.
     *
     * @return string HTML attribute
     **/
    protected function htmlWrapperAttributes() {
        $attributes = "id=\"{$this->formName}-{$this->name}\" ";

        $attributes .= "class=\"" . $this->htmlClasses() . "\"";

        $attributes .= ($this->title) ? " title=\"" . htmlentities($this->title, ENT_QUOTES) . "\"" : "";

        $attributes .= " data-errorMessage=\"" . htmlentities($this->errorMessage, ENT_QUOTES) . "\"";

        return $attributes;
    }

    /**
     * Returns value prepared for rendering the element to HTML
     *
     * @return mixed
     **/
    protected function htmlValue() {
        return ($this->value === null) ? $this->htmlDefaultValue() : $this->value;
    }

    protected function htmlErrorMessage() {
        if (!$this->valid
            && $this->value !== null
            && $this->errorMessage !== ""
        ) {
            $errorMessage = " <span class=\"errorMessage\">" . htmlentities($this->errorMessage, ENT_QUOTES) . "</span>";
        } else {
            $errorMessage = "";
        }

        return $errorMessage;
    }

    protected function htmlList($options = array()) {
        $htmlOptions = array();

        foreach($options as $index => $option) {
            if (is_string($index))  $index  = htmlentities($index, ENT_QUOTES);
            if (is_string($option)) $option = htmlentities($option, ENT_QUOTES);

            $htmlOptions[$index] = $option;
        }
        return $htmlOptions;
    }
}
