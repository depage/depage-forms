<?php 
/**
 * @file    htmlform.php
 * @brief   htmlform class and autoloader
 **/

/**
 * @mainpage
 *
 * depage-forms is PHP library for HTML form generation with focus on usability.
 * It is part of depage-cms, but it also works as a standalone tool. By abstracting
 * HTML, browser flaws (duplicate form submissions) and form validation, it
 * provides a comfortable way to obtain reliable data.
 *
 * @subpage features
 *
 * @subpage prerequesites
 *
 * @subpage license
 *
 * @subpage usage
 *
 * @subpage developer
 **/

/**
 * @page features Features
 *
 *      - validation
 *          - available for standard input elements
 *          - customizable with regular expressions
 *      - HTML5 form features
 *          - attributes        : placeholder, autofocus, textbox datalists, title,
 *                                pattern
 *          - input elements    : boolean (checkbox), email, hidden,
 *                                multiple (checkbox, select list), number, password,
 *                                range, search, single (radio, select list), tel, text,
 *                                textarea, url
 *      - averts form resubmission
 *      - divide forms into ѕeparate parts
 *      - neat return values with appropriate data types
 *      - easy language localisation
 *      - session timeout
 *      - simple creditcard validation (by values)
 **/

/**
 * @page prerequesites Prerequesites
 *      - PHP 5.3
 *      - PHPUnit 3.5 (included unit tests)
 *      - Doxygen 1.7.2 (to generate documentation)
 **/

/**
 * @page license License
 *      GPL2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *      MIT  http://www.opensource.org/licenses/mit-license.php
 **/

/**
 * @page usage Usage
 *
 * @link simple.php simple.php @endlink
 **/

/**
 * @page developer Developer guide
 *
 * @section style Coding style
 *
 * Generally, follow Zend coding style
 * camel case
 * 4 spaces indents
 * omit php closing tag
 *
 * @section Tests
 *
 * To run the tests:
 *
 * <pre>$ make tests</pre>
 **/

// {{{ namespace
/**
 * @namespace depage
 * @brief depage cms
 *
 * @namespace depage::htmlform
 * @brief htmlform class and autoloader
 *
 * @namespace depage::htmlform::abstracts
 * @brief Abstract element classes
 *
 * @namespace depage::htmlform::elements
 * @brief Subelement types
 *
 * @namespace depage::htmlform::exceptions
 * @brief Htmlform exceptions
 *
 * @namespace depage::htmlform::validators
 * @brief Validators for subelements
 **/
namespace depage\htmlform;

use depage\htmlform\elements;
// }}}

// {{{ autoloader
/**
 * @brief PHP autoloader
 *
 * Autoloads classes by namespace. (requires PHP >= 5.3)
 **/
function autoload($class) {
    $class = str_replace('\\', '/', str_replace(__NAMESPACE__ . '\\', '', $class));
    $file = __DIR__ . '/' .  $class . '.php';

    if (file_exists($file)) {
        require_once($file);
    }
}

spl_autoload_register(__NAMESPACE__ . '\autoload');
// }}}

/**
 * @brief main interface to users
 *
 * The class htmlform is the main tool of the htmlform library. It generates
 * input elements and container elements. It also contains the PHP session
 * handlers.
 **/
class htmlform extends abstracts\container {
    // {{{ variables
    /**
     * @brief HTML form method attribute
     * */
    protected $method;
    /**
     * @brief HTML form action attribute
     **/
    protected $submitURL;
    /**
     * @brief Specifies where the user is redirected to, once the form-data is valid
     **/
    protected $successURL;
    /**
     * @brief Contains the submit button label of the form
     **/
    protected $label;
    /**
     * @brief Contains the name of the array in the PHP session, holding the form-data
     **/
    protected $sessionSlotName;
    /**
     * @brief PHP session handle
     **/
    protected $sessionSlot;
    /**
     * @brief Contains current step number
     **/
    private $currentStepId;
    /**
     * @brief Contains array of step object references
     **/
    private $steps = array();
    /**
     * @brief Time until session expiry (seconds)
     **/
    protected $ttl;
    /**
     * @brief Form validation result/status
     **/
    public $valid;
    // }}}

    // {{{ __construct()
    /**
     * @brief   htmlform class constructor
     *
     * @param   $name       (string)    form name
     * @param   $parameters (array)     form parameters, HTML attributes
     * @param   $form       (object)    parent form object reference (not used in this case)
     * @return  void
     **/
    public function __construct($name, $parameters = array(), $form = null) {
        $this->url = parse_url($_SERVER['REQUEST_URI']);

        parent::__construct($name, $parameters, $this);

        $this->currentStepId = (isset($_GET['step'])) ? $_GET['step'] : 0;

        // check if there's an open session
        if (!session_id()) {
            session_start();
        }
        $this->sessionSlotName  = $this->name . '-data';
        $this->sessionSlot      =& $_SESSION[$this->sessionSlotName];

        $this->sessionExpiry();

        $this->valid = (isset($this->sessionSlot['formIsValid'])) ? $this->sessionSlot['formIsValid'] : null;

        // create a hidden input element to tell forms apart
        $this->addHidden('formName', array('defaultValue' => $this->name));
        $this->addChildElements();
    }
    // }}}

    // {{{ setDefaults()
    /**
     * @brief   Collects initial values across subclasses.
     *
     * @return  void
     **/
    protected function setDefaults() {
        parent::setDefaults();

        $this->defaults['label']        = 'submit';
        $this->defaults['submitURL']    = $_SERVER['REQUEST_URI'];
        $this->defaults['method']       = 'post';
        $this->defaults['successURL']   = $_SERVER['REQUEST_URI'];
        $this->defaults['validator']    = null;
        $this->defaults['ttl']          = null;
    }
    // }}}

    // {{{ sessionExpiry()
    /**
     * @brief   Deletes session when it expires.
     *
     * checks if session lifetime exceeds ttl value and deletes it. Updates
     * timestamp.
     *
     * @return  void
     **/
    private function sessionExpiry() {
        if (isset($this->ttl) && is_numeric($this->ttl)) {
            $timestamp = time();

            if (
                isset($this->sessionSlot['timestamp'])
                && ($timestamp - $this->sessionSlot['timestamp'] > $this->ttl)
            ) {
                $this->clearSession();
                $this->sessionSlot =& $_SESSION[$this->sessionSlotName];
            }

            $this->sessionSlot['timestamp'] = $timestamp;
        }
    }
    // }}}

    // {{{ addElement()
    /** 
     * @brief   Adds input or fieldset elements to htmlform.
     *
     * Calls parent class to generate an input element or a fieldset and add
     * it to its list of elements.
     * 
     * @param   $type       (string)    input type or fieldset
     * @param   $name       (string)    name of the element
     * @param   $parameters (array)     element attributes: HTML attributes, validation parameters etc.
     * @return  $newElement (object)    element object
     **/
    protected function addElement($type, $name, $parameters) {
        $this->checkElementName($name);

        $newElement = parent::addElement($type, $name, $parameters);

        if ($newElement instanceof elements\step)   { $this->steps[] = $newElement; }
        if ($newElement instanceof abstracts\input) { $this->updateInputValue($name); }

        return $newElement;
    }
    // }}}

    // {{{ updateInputValue()
    /**
     * @brief   Updates the value of an associated input element
     *
     * Sets the input elements' value. If there is post-data - we'll use that
     * to update the value of the input element and the session. If not - we 
     * take the value that's already in the session. If the value is neither in
     * the session nor in the post-data - nothing happens.
     *
     * @param   $name (string) name of the input element
     * @return  void
     **/
    public function updateInputValue($name) {
        // if it's a post, take the value from there and save it to the session
        if (
            isset($_POST['formName']) && ($_POST['formName'] === $this->name)
            && $this->inCurrentStep($name)
        ) {
            $value = (isset($_POST[$name])) ? $_POST[$name] : null;
            $this->sessionSlot[$name] = $this->getElement($name)->setValue($value);
        }
        // if it's not a post, try to get the value from the session
        else if (isset($this->sessionSlot[$name])) {
            $this->getElement($name)->setValue($this->sessionSlot[$name]);
        }
    }
    // }}}

    // {{{ inCurrentStep()
    /**
     * @brief   Checks if the element named $name is in the current step.
     *
     * @param   $name   (string)    name of element
     * @return          (bool)      says wether it's in the current step
     **/
    private function inCurrentStep($name) {
        return in_array($this->getElement($name), $this->getCurrentElements());
    }
    // }}}

    // {{{ setCurrentStep()
    /**
     * @brief   Validates step number of GET request.
     *
     * Validates step number of the GET request. If it's out of range it's
     * reset to the number of the first invalid step. (only to be used after
     * the form is completely created, because the step elements have to be
     * counted)
     *
     * @return  void
     **/
    private function setCurrentStep() {
        if (!is_numeric($this->currentStepId)
            || ($this->currentStepId > count($this->steps) - 1)
            || ($this->currentStepId < 0)
        ) {
            $this->currentStepId = $this->getFirstInvalidStep();
        }
    }
    // }}}

    // {{{ getCurrenElements()
    /**
     * @brief   Returns an array of input elements contained in the current step.
     *
     * @return  (array) element objects
     **/
    private function getCurrentElements() {
        $currentElements = array();

        foreach($this->elements as $element) {
            if ($element instanceof elements\fieldset) {
                if (
                    !($element instanceof elements\step)
                    || (isset($this->steps[$this->currentStepId]) && ($element == $this->steps[$this->currentStepId]))
                ) {
                    $currentElements = array_merge($currentElements, $element->getElements());
                }
            } else {
                $currentElements[] = $element;
            }
        }
        return $currentElements;
    }
    // }}}

    // {{{ __toString()
    /**
     * @brief   Renders form to HTML.
     *
     * Renders the htmlform object to HTML code. If the form contains elements
     * it calls their rendering methods.
     *
     * @return  (string) HTML code
     **/
    public function __toString() {
        $renderedElements   = '';
        $label              = $this->htmlLabel();
        $method             = $this->htmlMethod();
        $submitURL          = $this->htmlSubmitURL();

        foreach($this->elementsAndHtml as $element) {
            // leave out inactive step elements
            if (
                !($element instanceof elements\step)
                || (isset($this->steps[$this->currentStepId]) && $this->steps[$this->currentStepId] == $element)
            ) {
                $renderedElements .= $element;
            }
        }

        return "<form id=\"{$this->name}\" name=\"{$this->name}\" class=\"depage-form\" method=\"{$method}\" action=\"{$submitURL}\">" . "\n" .
            $renderedElements .
            "<p id=\"{$this->name}-submit\"><input type=\"submit\" value=\"{$label}\"></p>" . "\n" .
        "</form>";
    }
    // }}}

    // {{{ process()
    /**
     * @brief   Calls form validation and handles redirects.
     *
     * Implememts the Post/Redirect/Get strategy. Redirects to success Address
     * on succesful validation otherwise redirects to first invalid step or
     * back to form.
     *
     * @return  void
     *
     * @see     validate()
     **/
    public function process() {
        $this->setCurrentStep();

        // if there's post-data from this form
        if (isset($_POST['formName']) && ($_POST['formName'] === $this->name)) {
            if ($this->validate()) {
                $this->redirect($this->successURL);
            } else {
                $firstInvalidStep = $this->getFirstInvalidStep();
                $urlStepParameter = ($firstInvalidStep == 0) ? '' : '?step=' . $firstInvalidStep;
                $this->redirect($this->url['path'] . $urlStepParameter);
            }
        }
    }
    // }}}

    // {{{ getFirstInvalidStep()
    /**
     * @brief   Returns first step that didn't pass validation.
     *
     * Checks steps consecutively and returns the number of the first one that
     * isn't valid (steps need to be submitted at least once to count as valid).
     * Form must have been validated before calling this method.
     *
     * @return  $stepNumber (int) number of first invalid step
     **/
    private function getFirstInvalidStep() {
        if ( count($this->steps ) > 0) {
            foreach ( $this->steps as $stepNumber => $step ) {
                if ( !$step->valid ) {
                    return $stepNumber;
                }
            }
            /**
             * If there aren't any invalid steps there must be a fieldset
             * directly attached to the form that's invalid. In this case we
             * don't want to jump back to the first step. Hence, this little
             * hack.
             **/
            return count($this->steps) - 1;
        } else {
            return 0;
        }
    }
    // }}}

    // {{{ redirect()
    /**
     * @brief Redirects Browser to a different URL.
     *
     * @param $url (string) url to redirect to
     */
    public function redirect($url) {
        header('Location: ' . $url);
        die( "Tried to redirect you to " . $url);
    }
    // }}}

    // {{{ validate()
    /**
     * @brief   Validates the forms subelements.
     *
     * Form validation - validates form elements returns validation result and
     * writes it to session. Also calls custom validator if available.
     *
     * @return  (bool) validation result
     *
     * @see     process()
     **/
    public function validate() {
        // onValidate hook for custom required/validation rules
        $this->onValidate();

        parent::validate();

        if ($this->valid && is_callable($this->validator)) {
            $this->valid = call_user_func($this->validator, $this->getValues());
        }

        // save validation-state in session
        $this->sessionSlot['formIsValid'] = $this->valid;

        return $this->valid;
    }
    // }}}

    // {{{ isEmpty()
    /**
     * @brief   Returns wether form has been submitted before or not.
     *
     * @return  (bool) session status
     **/
    public function isEmpty() {
        return !isset($this->sessionSlot['formName']);
    }
    // }}}

    // {{{ populate()
    /**
     * @brief   Fills subelement values.
     *
     * Allows to manually populate the forms' input elements with values by
     * parsing an array of name-value pairs.
     *
     * @param   $data (array) input element names (key) and values (value)
     * @return  void
     **/
    public function populate($data = array()) {
        foreach($data as $name => $value) {
            $element = $this->getElement($name);
            if ($element) {
               $element->setDefaultValue($value);
            }
        }
    }
    // }}}

    // {{{ getValues()
    /**
     * @brief   Gets form-data from current PHP session.
     *
     * @return  (array) form-data
     **/
    public function getValues() {
        return (isset($this->sessionSlot)) ? $this->sessionSlot : null;
    }
    // }}}

    // {{{ checkElementName()
    /**
     * @brief   Checks for duplicate subelement names.
     *
     * Checks within the form if an input element or fieldset name is already
     * taken. If so, it throws an exception.
     *
     * @param   $name name to check
     * @return  void
     **/
    public function checkElementName($name) {
        foreach($this->getElements(true) as $element) {
            if ($element->getName() === $name) {
                throw new exceptions\duplicateElementNameException("Element name \"{$name}\" already in use.");
            }
        }
    }
    // }}}

    // {{{ clearSession()
    /**
     * @brief   Deletes the current forms' PHP session data.
     *
     * @return  void
     **/
    public function clearSession() {
        unset($_SESSION[$this->sessionSlotName]);
        unset($this->sessionSlot);
    }
    // }}}

    // {{{ onValidate()
    /**
     * @brief   Validation hook
     *
     * Can be overridden with custom validation rules, field-required rules etc.
     *
     * @return  void
     *
     * @see     validate()
     **/
    protected function onValidate() {}
    // }}}
}

/**
 * @example elements.php
 * @brief   Various element examples
 *
 * Demonstrates the use of all element types that are fully implemented.
 * Includes examples of element specific options.
 **/

/**
 * @example lists.php
 * @brief   Option list examples
 *
 * Multiple, single and text input elements have option lists. Here are some
 * more detailed examples.
 **/

/**
 * @example populate.php
 * @brief   Populate form example
 *
 * The htmlform->populate method provides a comfortable way to fill in
 * default values before displaying the form. It's more convenient than setting
 * the 'defaultValue' parameter for each input element individually.
 **/

/**
 * @example reallife.php
 * @brief   (almost) real life depage-forms example
 *
 * Contains a common personal information form. Some elements are set required
 * and the username has a minimum length.
 **/

/**
 * @example redirect.php
 * @brief   Redirect to success page example
 *
 * Once the form is validated, it can also be redirected to another URL. This
 * can be done by setting the 'successURL' parameter in the form.
 **/

/**
 * @example session-timeout.php
 * @brief   Form expiry example
 *
 * One can lower the lifetime of the forms session by setting the 'ttl' value.
 * Setting this higher than the PHP session length (default around 30min) has
 * no effect.
 **/

/**
 * @example simple.php
 * @brief   Simple form example
 *
 * Contains text and email input elements and demonstrates form processing and
 * validation. In this simple case none of the elements are set required, so an
 * empty form would also be valid.
 **/

/**
 * @example steps.php
 * @brief   Steps example
 *
 * This demonstrates dividing the form into separate pages using step
 * container elements. As usual the form itself can only be valid when all
 * its input elements are valid.
 * It is also possible to attach input elements directly to the form (outside
 * any step container). These elements are visible on every step.
 **/

/**
 * @example subclass.php
 * @brief   Form subclass example
 *
 * It's also possible to override the htmlform class and add elements in the
 * constructor. This way we can build reusable form classes or add custom rules
 * to the form validation.
 **/
