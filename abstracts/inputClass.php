<?php
/**
 * The abstract class inputClass holds the intersections of all implemented
 * HTML input element types. It handles validation, manual value manipulation
 * and constructor parameter checks.
 **/

namespace depage\htmlform\abstracts;

use depage\htmlform\validators;
use depage\htmlform\exceptions;

abstract class inputClass {
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
    public $value = null;
    /**
     * Holds validator object reference.
     **/
    protected $validator;
    /**
     * Contains current input elements' validation status.
     **/
    protected $valid = false;
    /**
     * HTML classes attribute for rendering the input element.
     **/
    protected $classes;
    /**
     * HTML autofocus attribute
     **/
    private $autofocus = false;

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

        $this->validator    = (isset($parameters['validator']))     ? new validators\validator($parameters['validator'])    : new validators\validator($this->type);
        $this->label        = (isset($parameters['label']))         ? $parameters['label']                                  : $name;
        $this->required     = (isset($parameters['required']))      ? $parameters['required']                               : false;
        $this->requiredChar = (isset($parameters['requiredChar']))  ? $parameters['requiredChar']                           : ' *';
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
     * @return void
     **/
    public function validate() {
        $this->valid = (($this->value !== null)
            && ($this->validator->match($this->value) || $this->emptyNotZero($this->value))
            && (!$this->emptyNotZero($this->value) || !$this->required)
        );
    }
    
    /**
     * Extends empty() function. Accepts '0' and false as not empty.
     * 
     * @param $value mixed value
     * @return bool
     **/
    protected function emptyNotZero($value) {
        return (empty($value) && ((string) $value !== '0') && ($value !== false));
    }

    /**
     * Returns the value of the current input elements' valid variable.
     *
     * @return $this->valid
     **/
    public function isValid() {
        return $this->valid;
    }

    /**
     * Allows to manually set the current input elements value.
     *
     * @param $newValue contains the new value
     * @return $newValue
     **/
    public function setValue($newValue) {
        $newValue = $this->typeCastValue($newValue);
        $this->value = $newValue;
        return $newValue;
    }

    /**
     * Returns the current input elements' value.
     *
     * @return $this->value
     **/
    public function getValue() {
        $this->value = $this->typeCastValue($this->value);
        return $this->value;
    }

    /**
     * Converts value to element specific type. (to be overridden by element
     * child classes)
     *
     * @param $value value to be converted
     * @return mixed converted value
     **/
    protected function typeCastValue($value) {
        return $value;
    }

    /**
     * Prepares element for HTML rendering and calls render() method.
     *
     * @return string of HTML rendered element
     **/
    public function __toString() {
        $value = ($this->value == null) ? $this->defaultValue : $this->value;
        $attributes = $this->getAttributes();
        $requiredChar = $this->getRequiredChar();
        $class = $this->getClasses();

        return $this->render($value, $attributes, $requiredChar, $class);
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
    protected function getClasses() {
        $classes = 'input-' . $this->type;
        
        if ($this->required) {
            $classes .= ' required';
        }
        if ($this->value !== null) {
            if (!$this->isValid()) {
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
    protected function getRequiredChar() {
        return ($this->required) ? "<em>$this->requiredChar</em>" : '';
    }

    /**
     * Returns string of HTML attributes. (required or autofocus)
     *
     * @return string HTML attribute
     **/
    protected function getAttributes() {
        $attributes = '';

        if ($this->required) {
            $attributes .= ' required';
        }
        if ($this->autofocus) {
            $attributes .= ' autofocus';
        }
        return $attributes;
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
        if (trim($name) === '') {
            throw new exceptions\invalidInputNameException();
        }
    }
}
