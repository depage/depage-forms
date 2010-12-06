<?php

require_once('validator.php');
require_once('textClass.php');
require_once('checkboxClass.php');

/**
 * The abstract class inputClass holds the intersections of all implemented
 * HTML input element types. It handles validation, manual value manipulation
 * and constructor parameter checks.
 **/
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
     * @param $name input elements' name
     * @param $parameters array of input element parameters, HTML attributes, validator specs etc.
     * @param $formName name of the parent HTML form. Used to identify the element once it's rendered.
     **/
    public function __construct($name, $parameters, $formName) {
        $this->_checkInputName($name);
        $this->_checkInputParameters($parameters);

        $this->type         = get_class($this);
        $this->name         = $name;
        $this->formName     = $formName;

        $this->validator    = (isset($parameters['validator']))     ? new validator($parameters['validator'])   : new validator($this->type);
        $this->label        = (isset($parameters['label']))         ? $parameters['label']                      : $name;
        $this->required     = (isset($parameters['required']))      ? $parameters['required']                   : false;
        $this->requiredChar = (isset($parameters['requiredChar']))  ? $parameters['requiredChar']               : ' *';
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
            && ($this->validator->match($this->value) || empty($this->value)) 
            && (!empty($this->value) || !$this->required)
        );
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
     * @return void
     **/
    public function setValue($newValue) {
        $this->value = $newValue;
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
     * Sets the HTML required attribute of the current input element.
     *
     * @return void
     **/
    public function setRequired() {
        $this->required = true;
    }
    
    /**
     * Prepares element for HTML rendering and calls render() method.
     *
     * @return string of HTML rendered element
     **/
    public function __toString() {
        $value = ($this->value == null) ? $this->defaultValue : $this->value;
        $requiredAttribute = $this->getRequiredAttribute();
        $requiredChar = $this->getRequiredChar();
        $class = $this->getClasses();

        return $this->render($value, $requiredAttribute, $requiredChar, $class);
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
     * Returns required attribute if current input element is required. 
     *
     * @return string HTML required attribute
     **/
    protected function getRequiredAttribute() {
        return ($this->required) ? ' required' : '';
    }

    
    /**
     * Throws an exception if $parameters isn't of type array.
     *
     * @param $parameters parameters for input element constructor
     * @return void
     **/
    private function _checkInputParameters($parameters) {
        if ((isset($parameters)) && (!is_array($parameters))) {
            throw new inputParametersNoArrayException();
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
            throw new inputNameNoStringException();
        }
        if (trim($name) === '') {
            throw new invalidInputNameException();
        }
    }
}
