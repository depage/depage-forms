<?php
/**
 * @file input.php
 * @brief abstract input element class
 **/

namespace depage\htmlform\abstracts;

use depage\htmlform\validators;

/**
 * @brief input element base class
 *
 * The abstract class input containѕ the intersections of all implemented
 * HTML input element types. It handles validation and value manipulation.
 **/
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
     * @brief   input class constructor
     *
     * @param   $name       (string)    input element name
     * @param   $parameters (array)     input element parameters, HTML attributes, validator specs etc.
     * @param   $form       (object)    parent form object
     * @return  void
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
     * @brief   Collects initial values across subclasses.
     *
     * @return  void
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
     * @brief   Validates input element
     *
     * Checks if the value the current input element holds is valid according
     * to it's validator object.
     *
     * @return  $this->valid (bool) validation result
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
     * @brief   custom validator call hook
     *
     * Hook method for validator call. Validator arguments can be adjusted on override.
     *
     * @return   (bool) validation result
     **/
    protected function validatorCall() {
        return $this->validator->validate($this->value);
    }

    /**
     * @brief   says wether the element value is empty
     *
     * Checks wether the input element value is empty. Accepts '0' and false as
     * not empty.
     *
     * @return  (bool) empty-check result
     **/
    protected function isEmpty() {
        return (
            empty($this->value)
            && ((string) $this->value !== '0') 
            && ($this->value !== false)
        );
    }

    /**
     * @brief   set the input element value
     *
     * Allows to manually set the current input elements value. Additionally
     * performs typecasting to element specific datatype.
     *
     * @param   $newValue       (mixed) new value
     * @return  $this->value    (mixed) typecasted new value
     *
     * @see     typeCastValue()
     **/
    public function setValue($newValue) {
        $this->value = $newValue;
        $this->typeCastValue();
        return $this->value;
    }

    /**
     * @brief   Returns the current input elements' value.
     *
     * @return  $this->value (mixed) input element value
     **/
    public function getValue() {
        return $this->value;
    }

    /**
     * @brief   set the initial input element value
     *
     * Since the actual іnput value has to be null initially (for validation
     * purposes) the user can manually set the default value here.
     *
     * @param   $newDefaultValue (mixed) new value
     * @return  void
     *
     * @see     htmlValue()
     **/
    public function setDefaultValue($newDefaultValue) {
        $this->defaultValue = $newDefaultValue;
    }

    /**
     * @brief converts element value
     *
     * Converts value to element specific datatype. (to be overridden by
     * element child classes)
     *
     * @return void
     **/
    protected function typeCastValue() {}

    /**
     * @brief   Sets the HTML autofocus-attribute of the current input element.
     *
     * @param   $autofocus (bool) HTML autofocus-attribute
     * @return  void
     **/
    public function setAutofocus($autofocus = true) {
        $this->autofocus = (bool) $autofocus;
    }

    /**
     * @brief   Sets the HTML required-attribute of the current input element.
     *
     * @param   $required (bool) HTML required-attribute
     * @return  void
     **/
    public function setRequired($required = true) {
        $this->required = (bool) $required;
    }

    /**
     * @brief   Returns string of the elements' HTML-classes, separated by spaces.
     *
     * @return  $classes (string) HTML-classes
     **/
    protected function htmlClasses() {
        $classes = 'input-' . $this->htmlEscape($this->type);
        
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
     * @brief   Returns elements' required-indicator.
     *
     * If the current input element is set required it returns the marker.
     * Otherwise an empty string.
     *
     * @return  $this->marker (string) marker or empty string
     **/
    protected function htmlMarker() {
        return ($this->required) ? " <em>" . $this->htmlEscape($this->marker) . "</em>" : "";
    }

    /**
     * @brief   Returns string of HTML attributes for input element.
     *
     * @return  $attributes (string) HTML attribute
     **/
    protected function htmlInputAttributes() {
        $attributes = '';

        if ($this->required)    $attributes .= " required";
        if ($this->autofocus)   $attributes .= " autofocus";

        return $attributes;
    }

    /**
     * @brief   Returns string of HTML attributes for element wrapper paragraph.
     *
     * @return  $attributes (string) HTML attribute
     **/
    protected function htmlWrapperAttributes() {
        $attributes = "id=\"{$this->formName}-{$this->name}\" ";

        $attributes .= "class=\"" . $this->htmlClasses() . "\"";

        $attributes .= ($this->title) ? " title=\"" . $this->htmlEscape($this->title) . "\"" : "";

        $attributes .= " data-errorMessage=\"" . $this->htmlEscape($this->errorMessage) . "\"";

        return $attributes;
    }

    /**
     * @brief   Returns HTML-rendered element value
     *
     * @return  (mixed) element value
     **/
    protected function htmlValue() {
        return ($this->value === null) ? $this->htmlEscape($this->defaultValue) : $this->value;
    }

    /**
     * @brief   Returns HTML-rendered error message
     *
     * @return  $errorMessage (string) HTML-rendered error message
     **/
    protected function htmlErrorMessage() {
        if (!$this->valid
            && $this->value !== null
            && $this->errorMessage !== ""
        ) {
            $errorMessage = " <span class=\"errorMessage\">" . $this->htmlEscape($this->errorMessage) . "</span>";
        } else {
            $errorMessage = "";
        }

        return $errorMessage;
    }
}
