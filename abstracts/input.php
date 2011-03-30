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
    protected $autofocus = false;
    /**
     * HTML pattern attribute
     **/
    protected $pattern = false;

    /**
     * @param $name input elements' name
     * @param $parameters array of input element parameters, HTML attributes, validator specs etc.
     * @param $formName name of the parent HTML form. Used to identify the element once it's rendered.
     **/
    public function __construct($name, &$parameters, $formName) {

        $this->log          = (isset($parameters['log']))           ? $parameters['log']            : null;

        $this->_checkInputName($name);
        $this->_checkInputParameters($parameters);

        $this->type         = strtolower(str_replace('depage\\htmlform\\elements\\', '', get_class($this)));
        $this->name         = $name;
        $this->formName     = $formName;

        // converts index to lower case so parameters are case independent
        $parameters = array_change_key_case($parameters);

        $this->validator    = (isset($parameters['validator']))
            ? validators\validator::factory($parameters['validator'], $this->log)
            : validators\validator::factory($this->type, $this->log);

        $this->autofocus    = (isset($parameters['autofocus']))     ? $parameters['autofocus']      : false;
        $this->label        = (isset($parameters['label']))         ? $parameters['label']          : $this->name;
        $this->required     = (isset($parameters['required']))      ? $parameters['required']       : false;
        $this->marker       = (isset($parameters['marker']))        ? $parameters['marker']         : '*';
        $this->errorMessage = (isset($parameters['errormessage']))  ? $parameters['errormessage']   : 'Please enter valid data!';
        $this->title        = (isset($parameters['title']))         ? $parameters['title']          : false;
    }

    /**
     * Returns respective HTML escaped attributes for element rendering.
     **/
    public function __call($functionName, $functionArguments) {
        if (substr($functionName, 0, 4) === 'html') {
            $attribute = str_replace('html', '', $functionName);
            $attribute{0} = strtolower($attribute{0});

            return htmlentities($this->$attribute, ENT_QUOTES);
        } else {
            trigger_error("Call to undefined method $functionName", E_USER_ERROR);
        }
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
        return ($this->value === null) ? $this->defaultValue : $this->value;
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

    protected function htmlList($options) {
        if (is_array($options)) {
            $htmlOptions = array();

            foreach($options as $index => $option) {
                if (is_string($index))  $index  = htmlentities($index, ENT_QUOTES);
                if (is_string($option)) $option = htmlentities($option, ENT_QUOTES);

                $htmlOptions[$index] = $option;
            }
            return $htmlOptions;
        }
        return $options;
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

    protected function log($argument, $type) {
        if (is_callable(array($this->log, 'log'))) {
            $this->log->log($argument, $type);
        } else {
            error_log($argument);
        }
    }
}
