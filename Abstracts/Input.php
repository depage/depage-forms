<?php
/**
 * @file input.php
 * @brief abstract input element class
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace Depage\HtmlForm\Abstracts;

use Depage\HtmlForm\Validators;

/**
 * @brief input element base class
 *
 * The abstract class input containѕ the intersections of all implemented
 * HTML input element types. It handles validation and value manipulation.
 **/
abstract class Input extends Element
{
    // {{{ variables
    /**
     * @brief Input element type - HTML input type attribute.
     **/
    protected $type;

    /**
     * @addtogroup htmlformInputDefaults
     *
     * @default string $label
     *     Sets the label for the input
     **/
    /**
     * @brief Input element - HTML label
     **/
    protected $label;

    /**
     * @addtogroup htmlformInputDefaults
     *
     * @default string $marker
     *     Sets marker character(s) that gets display on required fields.
     **/
    /**
     * @brief Input element - HTML marker text that marks required fields
     **/
    protected $marker;

    /**
     * @addtogroup htmlformInputDefaults
     *
     * @default bool $required
     *     Sets wether an input is required to hold a value to be valid
     **/
    /**
     * @brief True if the input element is required to hold a value to be valid.
     **/
    protected $required;

    /**
     * @addtogroup htmlformInputDefaults
     *
     * @default bool $disabled
     *     Enables/disables an input
     **/
    /**
     * @brief wether a input element will be disabled
     **/
    protected $disabled;

    /**
     * @addtogroup htmlformInputDefaults
     *
     * @default bool $readonly
     *     Marks an input as readonly
     **/
    /**
     * @brief wether a input element will be readonly
     **/
    protected $readonly;

    /**
     * @brief Name of the parent HTML form. Used to identify the element once it's rendered.
     **/
    protected $formName;

    /**
     * @brief Input elements's value.
     **/
    protected $value = null;

    /**
     * @addtogroup htmlformInputDefaults
     *
     * @default string|function $validator
     *     Sets the validator for the input.
     *     - When $validator starts and ends with "/" e.g. "/[a-z0-9]/" the
     *       validator will be a Depage::HtmlForm::Validators::RegEx
     *     - When it is a plain string it will be a the validator with the name
     *       $validator out of the namespace Depage::HtmlForm::Validators.
     *     - If the $validator is callable then the validator will be a
     *       Depage::HtmlForm::Validators::Closure
     **/
    /**
     * @brief Holds validator object reference.
     **/
    protected $validator;

    /**
     * @addtogroup htmlformInputDefaults
     *
     * @default string $class
     *     Class name or class names that gets added to wrapper paragraph.
     **/
    /**
     * @brief class for paragraph
     **/
    protected $class;

    /**
     * @brief HTML classes attribute for rendering the input element.
     **/
    protected $classes;

    /**
     * @addtogroup htmlformInputDefaults
     *
     * @default bool $autofocus
     *     sets current input to get autofocus on a page. If you set the
     *     autofocus to more than one input - The first one gets the focus.
     **/
    /**
     * @brief HTML autofocus attribute
     **/
    protected $autofocus = false;

    /**
     * @addtogroup htmlformInputDefaults
     *
     * @default bool $autocapitalize
     *     Enables/disables autocapitalizion (mostly on mobile devices)
     **/
    /**
     * @brief HTML autocapitalize attribute
     */
    protected $autocapitalize;

    /**
     * @addtogroup htmlformInputDefaults
     *
     * @default bool $autocorrect
     *      Enables/disables autocorrection
     **/
    /**
     * @brief HTML autocorrect attribute
     */

    protected $autocorrect;
    /**
     * @addtogroup htmlformInputDefaults
     *
     * @default bool $autocomplete
     *     Used to enable/disable autocorrect
     **/
    /**
     * @brief HTML autocomplete attribute
     */
    protected $autocomplete;

    /**
     * @brief HTML pattern attribute
     **/
    protected $pattern;

    /**
     * @addtogroup htmlformInputDefaults
     *
     * @default string $errorMessage
     *     Sets the message that will be displayed in case of invalid input
     **/
    /**
     * @brief Message that gets displayed in case of invalid input
     **/
    protected $errorMessage;

    /**
     * @addtogroup htmlformInputDefaults
     *
     * @default string $helpMessage
     *     Optional help message that may be used for extra information e.g. tooltips.
     *     These string will be html escaped.
     **/
    /**
     * @brief Extra help message
     **/
    protected $helpMessage;

    /**
     * @addtogroup htmlformInputDefaults
     *
     * @default string $helpMessageHtml
     *     Optional help message that may be used for extra information e.g. tooltips.
     *     These string won't be escaped as therefor has to be valid html.
     **/
    /**
     * @brief Extra help message in html format
     **/
    protected $helpMessageHtml;

    /**
     * @addtogroup htmlformInputDefaults
     *
     * @default variant $dataInfo
     *     Optional info for the data saved in the element.
     *     This value isn't used internally in the htmlform, but can be used
     *     to add extra data to an element that can be read out at a later time.
     *     E.g. you can add an object- or an xml-xpath to the element.
     **/
    /**
     * @brief Extra information about the data that is saved inside the element.
     **/
    public $dataInfo;
    // }}}

    // {{{ __construct()
    /**
     * @brief   input class constructor
     *
     * @param  string $name       input element name
     * @param  array  $parameters input element parameters, HTML attributes, validator specs etc.
     * @param  object $form       parent form object
     * @return void
     **/
    public function __construct($name, $parameters, $form)
    {
        $this->type         = str_replace('Depage\\HtmlForm\\Elements\\', '', get_class($this));
        $this->formName     = $form->getName();

        parent::__construct($name, $parameters, $form);

        $this->validator    = (isset($parameters['validator']))
            ? Validators\Validator::factory($parameters['validator'], $this->log)
            : Validators\Validator::factory($this->type, $this->log);
    }
    // }}}

    // {{{ setDefaults()
    /**
     * @brief  Sets the default values for input elements
     *
     * The constructor loops through these and creates settable class
     * attributes at runtime. It's a compact mechanism for initialising
     * a lot of variables.
     *
     * @return void
     **/
    protected function setDefaults()
    {
        parent::setDefaults();

        $this->defaults['autocapitalize']  = null;
        $this->defaults['autocomplete']    = null;
        $this->defaults['autocorrect']     = null;
        $this->defaults['autofocus']       = false;
        $this->defaults['errorMessage']    = _('Please enter valid data');
        $this->defaults['label']           = $this->name;
        $this->defaults['labelHtml']       = '';
        $this->defaults['marker']          = '*';
        $this->defaults['required']        = false;
        $this->defaults['disabled']        = false;
        $this->defaults['readonly']        = false;
        $this->defaults['title']           = false;
        $this->defaults['class']           = '';
        $this->defaults['lang']            = '';
        $this->defaults['helpMessage']     = '';
        $this->defaults['helpMessageHtml'] = '';
        $this->defaults['dataInfo']        = null;
    }
    // }}}

    // {{{ validate()
    /**
     * @brief   Validates input element
     *
     * Checks if the value the current input element holds is valid according
     * to it's validator object.
     *
     * @return bool $this->valid validation result
     **/
    public function validate()
    {
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
    // }}}

    // {{{ invalidate()
    /**
     * @brief Invalidates input
     *
     * Thats valid state of input to false. Mainly to use in onValidate
     * function to invalidate input based on other fields values.
     *
     * @return void
     **/
    public function invalidate()
    {
        $this->validated = true;
        $this->valid = false;

        return $this;
    }
    // }}}

    // {{{ validatorCall()
    /**
     * @brief   custom validator call hook
     *
     * Hook method for validator call. Validator arguments can be adjusted on override.
     *
     * @return bool validation result
     **/
    protected function validatorCall()
    {
        return $this->validator->validate($this->value);
    }
    // }}}

    // {{{ isEmpty()
    /**
     * @brief   says wether the element value is empty
     *
     * Checks wether the input element value is empty. Accepts '0' and false as
     * not empty.
     *
     * @return bool empty-check result
     **/
    public function isEmpty()
    {
        return (
            empty($this->value)
            && $this->value !== '0'
            && $this->value !== false
        );
    }
    // }}}

    // {{{ setValue()
    /**
     * @brief   set the input element value
     *
     * Sets the current input elements value. Additionally performs typecasting
     * to element specific datatype.
     *
     * @param  mixed $newValue new value
     * @return mixed $this->value    typecasted new value
     *
     * @see     typeCastValue()
     **/
    public function setValue($newValue)
    {
        $this->value = $newValue;
        $this->typeCastValue();
        $this->validated = false;

        return $this->value;
    }
    // }}}

    // {{{ getValue()
    /**
     * @brief   Returns the current input elements' value.
     *
     * @return mixed $this->value input element value
     **/
    public function getValue()
    {
        return $this->value;
    }
    // }}}

    // {{{ setLabel()
    /**
     * @brief set the label of the input
     **/
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }
    // }}}
    // {{{ getLabel()
    /**
     * @brief   Returns the current input elements' label.
     *
     * @return  $this->label HTML label
     **/
    public function getLabel()
    {
        return $this->label;
    }
    // }}}

    // {{{ clearValue()
    /**
     * @brief   resets the value to null
     *
     * @return void
     **/
    public function clearValue()
    {
        $this->value = null;

        return $this->value;
    }
    // }}}

    // {{{ setDefaultValue()
    /**
     * @brief   set the initial input element value
     *
     * Since the actual іnput value has to be null initially (for validation
     * purposes) the user can manually set the default value here.
     *
     * @param  mixed $newDefaultValue new value
     * @return void
     *
     * @see     htmlValue()
     **/
    public function setDefaultValue($newDefaultValue)
    {
        $this->defaultValue = $newDefaultValue;

        return $this;
    }
    // }}}

    // {{{ getDefaultValue()
    /**
     * @brief   gets the initial input element value
     *
     * @return  value
     **/
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }
    // }}}

    // {{{ typeCastValue()
    /**
     * @brief converts element value
     *
     * Converts value to element specific datatype. (to be overridden by
     * element child classes)
     *
     * @return void
     **/
    protected function typeCastValue() {}
    // }}}

    // {{{ setAutofocus()
    /**
     * @brief   Sets the HTML autofocus-attribute of the current input element.
     *
     * @param       $autofocus (bool) HTML autofocus-attribute
     * @return void
     **/
    public function setAutofocus($autofocus = true)
    {
        $this->autofocus = (bool) $autofocus;

        return $this;
    }
    // }}}

    // {{{ setRequired()
    /**
     * @brief   Sets the HTML required-attribute of the current input element.
     *
     * @param  bool $required HTML required-attribute
     * @return void
     **/
    public function setRequired($required = true)
    {
        $this->required = (bool) $required;
        $this->validated = false;

        return $this;
    }
    // }}}

    // {{{ htmlClasses()
    /**
     * @brief   Returns string of the elements' HTML-classes, separated by spaces.
     *
     * @return string $classes HTML-classes
     **/
    protected function htmlClasses()
    {
        $classes = 'input-' . $this->htmlEscape(strtolower($this->type));

        if ($this->required) {
            $classes .= ' required';
        }
        if ($this->disabled) {
            $classes .= ' disabled';
        }
        if ($this->readonly) {
            $classes .= ' readonly';
        }
        if (($this->value !== null) && (!$this->validate())) {
            $classes .= ' error';
        }
        if (isset($this->skin)) {
            $classes .= ' skin-' . $this->htmlEscape($this->skin);
        }
        if (!empty($this->class)) {
            $classes .= ' ' . $this->htmlEscape($this->class);
        }

        return $classes;
    }
    // }}}

    // {{{ htmlMarker()
    /**
     * @brief   Returns elements' required-indicator.
     *
     * If the current input element is set required it returns the marker.
     * Otherwise an empty string.
     *
     * @return string $this->marker marker or empty string
     **/
    protected function htmlMarker()
    {
        return ($this->required) ? " <em>" . $this->htmlEscape($this->marker) . "</em>" : "";
    }
    // }}}

    // {{{ htmlInputAttributes()
    /**
     * @brief   Returns string of HTML attributes for input element.
     *
     * @return string $attributes HTML attribute
     **/
    protected function htmlInputAttributes()
    {
        $attributes = '';

        if ($this->required)    $attributes .= ' required="required"';
        if ($this->disabled)    $attributes .= ' disabled="disabled"';
        if ($this->readonly)    $attributes .= ' readonly="readonly"';
        if ($this->autofocus)   $attributes .= ' autofocus="autofocus"';

        $autoAttributes = array(
            "autocapitalize",
            "autocorrect",
        );
        foreach ($autoAttributes as $attr) {
            if (!is_null($this->$attr)) {
                if ($this->$attr) {
                    $attributes .= " $attr=\"on\"";
                } else {
                    $attributes .= " $attr=\"off\"";
                }
            }
        }
        // enable autocomplete attribute according to
        // https://html.spec.whatwg.org/multipage/forms.html#autofill
        if (!is_null($this->autocomplete)) {
            if (is_string($this->autocomplete)) {
                $attributes .= " autocomplete=\"" . $this->htmlEscape($this->autocomplete) . "\"";
            } else if ($this->autocomplete === true) {
                $attributes .= " autocomplete=\"on\"";
            } else {
                $attributes .= " autocomplete=\"off\"";
            }
        }

        return $attributes;
    }
    // }}}

    // {{{ htmlDataAttributes()
    /**
     * @brief   Returns dataAttr escaped as attribute string
     **/
    protected function htmlDataAttributes()
    {
        $this->dataAttr['errorMessage'] = $this->errorMessage;

        return parent::htmlDataAttributes();
    }
    // }}}

    // {{{ htmlWrapperAttributes()
    /**
     * @brief   Returns string of HTML attributes for element wrapper paragraph.
     *
     * @return string $attributes HTML attribute
     **/
    protected function htmlWrapperAttributes()
    {
        $attributes = "id=\"{$this->formName}-{$this->name}\" ";

        $attributes .= "class=\"" . $this->htmlClasses() . "\"";

        $attributes .= ($this->title) ? " title=\"" . $this->htmlEscape($this->title) . "\"" : "";

        $attributes .= ($this->lang) ? " lang=\"" . $this->htmlEscape($this->lang) . "\"" : "";

        $attributes .= $this->htmlDataAttributes();

        return $attributes;
    }
    // }}}

    // {{{ htmlValue()
    /**
     * @brief   Returns HTML-rendered element value
     *
     * @return mixed element value
     **/
    protected function htmlValue()
    {
        return $this->htmlEscape($this->value === null ? $this->defaultValue : $this->value);
    }
    // }}}

    // {{{ htmlErrorMessage()
    /**
     * @brief   Returns HTML-rendered error message
     *
     * @return string $errorMessage HTML-rendered error message
     **/
    protected function htmlErrorMessage()
    {
        if (!$this->valid
            && $this->value !== null
            && $this->errorMessage !== ""
        ) {
            $errorMessage = "<span class=\"errorMessage\">" . $this->htmlEscape($this->errorMessage) . "</span>";
        } else {
            $errorMessage = "";
        }

        return $errorMessage;
    }
    // }}}

    // {{{ htmlHelpMessage()
    /**
     * @brief   Returns HTML-rendered helpMessage
     *
     * @return string $helpMessage HTML-rendered helpMessage span
     **/
    protected function htmlHelpMessage()
    {
        $helpMessage = '';
        if (isset($this->helpMessage) && !empty($this->helpMessage)) {
            // escaped message
            $helpMessage = "<span class=\"helpMessage\">" . $this->htmlEscape($this->helpMessage) . "</span>";
        }
        if (isset($this->helpMessageHtml) && !empty($this->helpMessageHtml)) {
            // html message
            $helpMessage = "<span class=\"helpMessage\">" . $this->helpMessageHtml. "</span>";
        }

        return $helpMessage;
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
