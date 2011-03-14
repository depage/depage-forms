<?php
/**
 * The abstract class inputClass holds the intersections of all implemented
 * HTML input element types. It handles validation, manual value manipulation
 * and constructor parameter checks.
 **/

namespace depage\htmlform\abstracts;

use depage\htmlform\validators;
use depage\htmlform\exceptions;

abstract class input {
    /**
     * Input element type - HTML input type attribute.
     **/
    protected $type;
    /**
     * Input element name.
     **/
    protected $name;
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
     * Contains current input elements' validation status.
     **/
    protected $valid = false;
    /**
     * True if the element has been validated before.
     **/
    protected $validated = false;
    /**
     * HTML classes attribute for rendering the input element.
     **/
    protected $classes;
    /**
     * HTML autofocus attribute
     **/
    private $autofocus = false;
    /**
     * HTML pattern attribute
     **/
    protected $pattern = false;

    /**
     * @param $name input elements' name
     * @param $parameters array of input element parameters, HTML attributes, validator specs etc.
     * @param $formName name of the parent HTML form. Used to identify the element once it's rendered.
     **/
    public function __construct($name, $parameters, $formName) {
        $this->_checkInputName($name);
        $this->_checkInputParameters($parameters);

        $this->type         = strtolower(str_replace('depage\\htmlform\\elements\\', '', get_class($this)));
        $this->name         = $name;
        $this->formName     = $formName;

        $this->validator    = (isset($parameters['validator']))     ? validators\validator::factory($parameters['validator'])       : validators\validator::factory($this->type);
        $this->label        = (isset($parameters['label']))         ? $parameters['label']                                          : $this->name;
        $this->required     = (isset($parameters['required']))      ? $parameters['required']                                       : false;
        $this->requiredChar = (isset($parameters['requiredChar']))  ? $parameters['requiredChar']                                   : '*';
        $this->errorMessage = (isset($parameters['errorMessage']))  ? $parameters['errorMessage']                                   : 'Please enter valid data!';
    }

    /**
     * Returns the name of current input element.
     *
     * @return $this->name
     **/
    public function getName() {
        return $this->name;
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
    public function setAutofocus() {
        $this->autofocus = true;
    }

    /**
     * Sets the HTML required attribute of the current input element.
     *
     * @return void
     **/
    public function setRequired() {
        $this->required = true;
    }

    /**
     * Unsets the the HTML required attribute of the current input element.
     *
     * @return void
     **/
    public function setNotRequired() {
        $this->required = false;
    }

    /**
     * Returns a string of the HTML elements classes, separated by a space.
     *
     * @return $classes
     **/
    protected function htmlClasses() {
        $classes = 'input-' . $this->type;
        
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
     * @return $this->requiredChar or empty string
     **/
    protected function htmlRequiredChar() {
        return ($this->required) ? " <em>$this->requiredChar</em>" : '';
    }

    /**
     * Returns string of HTML attributes. (required or autofocus)
     *
     * @return string HTML attribute
     **/
    protected function htmlAttributes() {
        $attributes = '';

        if ($this->required)    $attributes .= " required";
        if ($this->autofocus)   $attributes .= " autofocus";

        $attributes .= $this->validator->getPatternAttribute();

        return $attributes;
    }

    /**
     * Returns value prepared for rendering the element to HTML
     *
     * @return mixed
     **/
    protected function htmlValue() {
        return ($this->value === null) ? $this->defaultValue : $this->value;
    }

    protected function htmlErrorMessage() {
        if (!$this->valid
            && $this->value !== null
            && $this->errorMessage !== ""
        ) {
            $htmlErrorMessage = " <span class=\"errorMessage\">$this->errorMessage</span>";
        } else {
            $htmlErrorMessage = "";
        }

        return $htmlErrorMessage;
    }

    /**
     * Throws an exception if $parameters isn't of type array.
     *
     * @param $parameters parameters for input element constructor
     * @return void
     **/
    private function _checkInputParameters($parameters) {
        if ((isset($parameters)) && (!is_array($parameters))) {
            throw new exceptions\inputParametersNoArrayException();
        }
    }
    
    /**
     * Checks if name of input element is of type string and not empty.
     * Otherwise throws an exception.
     * 
     * @params $name name of input element
     * @return void
     **/
    private function _checkInputName($name) {
        if (!is_string($name)) {
            throw new exceptions\inputNameNoStringException();
        }
        if ((trim($name) === '') || preg_match('/[^a-zA-Z0-9_]/', $name))  {
            throw new exceptions\invalidInputNameException();
        }
    }
}
