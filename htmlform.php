<?php 

/**
 * htmlform is a PHP library that assists programmers in creating HTML forms.
 * It features validation and takes care of duplicate form submissions.
 **/
namespace depage\htmlform; 

use depage\htmlform\elements;

function autoload($class) {
    $class = str_replace('\\', '/', str_replace(__NAMESPACE__ . '\\', '', $class));
    $file = __DIR__ . '/' .  $class . '.php';

    if (file_exists($file)) {
        require_once($file);
    }
}

spl_autoload_register(__NAMESPACE__ . '\autoload');

/**
 * The class htmlform is the main tool of the htmlform library. It generates HTML
 * fieldsets and input elements. It also contains the PHP session handlers.
 **/
class htmlform extends abstracts\container {
    /**
     * HTML form method attribute.
     * */
    protected $method;
    /**
     * HTML form action attribute.
     **/
    protected $action;
    /**
     * Specifies where the user is redirected once the form-data is valid.
     **/
    protected $successAddress;
    /**
     * Contains the submit button label of the form.
     **/
    protected $submitLabel;
    /**
     * Contains the name of the array in the PHP session holding the form-data.
     **/
    protected $sessionSlotName;
    /**
     * PHP session handle.
     **/
    protected $sessionSlot;
    /**
     * Contains current step number.
     **/
    protected $currentStepId;
    /**
     * Contains array of step object references.
     **/
    protected $steps = array();
    /**
     * Time for session expiry
     **/
    protected $ttl;

    /**
     * @param $name string - form name
     * @param $parameters array of form parameters, HTML attributes
     * @return void
     **/
    public function __construct($name, $parameters = array()) {
        $this->url = parse_url($_SERVER['REQUEST_URI']);

        parent::__construct($name, $parameters);

        $this->submitLabel      = (isset($parameters['submitLabel']))       ? $parameters['submitLabel']    : 'submit';
        $this->action           = (isset($parameters['action']))            ? $parameters['action']         : $_SERVER['REQUEST_URI'];
        $this->method           = (isset($parameters['method']))            ? $parameters['method']         : 'post';
        $this->successAddress   = (isset($parameters['successAddress']))    ? $parameters['successAddress'] : $_SERVER['REQUEST_URI'];
        $this->validator        = (isset($parameters['validator']))         ? $parameters['validator']      : null;
        $this->currentStepId    = (isset($_GET['step']))                    ? $_GET['step']                 : 0;
        $this->ttl              = (isset($parameters['ttl']))               ? $parameters['ttl']            : null;

        // check if there's an open session
        if (!session_id()) {
            session_start();
        }
        $this->sessionSlotName  = $this->name . '-data';
        $this->sessionSlot      =& $_SESSION[$this->sessionSlotName];

        $this->sessionExpiry();

        // create a hidden input element to tell forms apart
        $this->addHidden('formName', array('defaultValue' => $this->name));
    }

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
    
    /** 
     * Calls parent class to generate an input element or a fieldset and add
     * it to its list of elements.
     * 
     * @param $type input type or fieldset
     * @param $name string - name of the element
     * @param $parameters array of element attributes: HTML attributes, validation parameters etc.
     * @return object $newInput
     **/
    public function addElement($type, $name, $parameters = array()) {
        $this->checkElementName($name);

        $newElement = parent::addElement($type, $name, $parameters);

        if ($newElement instanceof elements\fieldset) {
            // if it's a fieldset it needs to know which form it belongs to
            $newElement->setParentForm($this);

            if ($newElement instanceof elements\step) {
                $this->steps[] = $newElement;
            }
        } else {
            $this->updateInputValue($name);
        }

        return $newElement;
    }

    /**
     * Sets the input elements' value. If there is post-data - we'll use that
     * to update the value of the input element and the session. If not - we 
     * take the value that's already in the session. If the value is neither in
     * the session nor in the post-data - nothing happens.
     *
     * @param $name the name of the input element we're looking for
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
    
    /**
     * Checks if the element named $name is in the current step.
     *
     * @param $name name of element
     * @return (bool) - says wether it's in the current step
     **/
    private function inCurrentStep($name) {
        return in_array($this->getElement($name), $this->getCurrentElements());
    }

    /**
     * Validates step number of the GET request. If it's out of range it's
     * reset to the number of the first invalid step. (only to be used after
     * the form is completely created, because the step elements have to be
     * counted)
     **/
    private function setCurrentStep() {
        if (!is_numeric($this->currentStepId)
            || ($this->currentStepId > count($this->steps) - 1)
            || ($this->currentStepId < 0)
        ) {
            $this->currentStepId = $this->getFirstInvalidStep();
        }
    }

    /**
     * returns an array of input elements contained in the current step.
     *
     * @return (array) input-elements
     **/
    private function getCurrentElements() {
        $currentElements = array();

        foreach($this->elements as $element) {
            if ($element instanceof elements\fieldset) {
                if (
                    !($element instanceof elements\step)
                    || ($element == $this->steps[$this->currentStepId])
                ) {
                    $currentElements = array_merge($currentElements, $element->getElements());
                }
            } else {
                $currentElements[] = $element;
            }
        }
        return $currentElements;
    }

    /**
     * Renders the form as HTML code. If the form contains elements it
     * calls their rendering methods.
     *
     * @return (string)
     **/
    public function __toString() {
        $renderedElements = '';
        foreach($this->elementsAndHtml as $element) {
            // leave out inactive step elements
            if (!($element instanceof elements\step) || (isset($this->steps[$this->currentStepId]) && $this->steps[$this->currentStepId] == $element)) {
                $renderedElements .= $element;
            }
        }
        $renderedSubmit = "<p id=\"$this->name-submit\"><input type=\"submit\" name=\"submit\" value=\"$this->submitLabel\"></p>";

        return "<form id=\"$this->name\" name=\"$this->name\" method=\"$this->method\" action=\"$this->action\">" .
            $renderedElements . $renderedSubmit .
        "</form>";
    }

    /**
     * Implememts the Post/Redirect/Get strategy. Redirects to success Address
     * on succesful validation otherwise redirects to first invalid step or
     * back to form.
     **/
    public function process() {
        $this->setCurrentStep();

        // if there's post-data from this form
        if (isset($_POST['formName']) && ($_POST['formName'] === $this->name)) {
            if ($this->validate()) {
                $this->redirect($this->successAddress);
            } else {
                $firstInvalidStep = $this->getFirstInvalidStep();
                $urlStepParameter = ($firstInvalidStep == 0) ? '' : '?step=' . $firstInvalidStep;
                $this->redirect($this->url['path'] . $urlStepParameter);
            }
        }
    }

    /**
     * Checks steps consecutively and returns the number of the first one that
     * isn't valid (steps need to be submitted at least once to count as valid).
     *
     * Beware! Be sure to validate the form before calling this method.
     *
     * @return (int) $stepNumber
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

    /**
     * Redirects Browser to a different URL
     *
     * @param   $url    string - url to redirect to
     */
    public function redirect($url) {
        header('Location: ' . $url);
        die( "Tried to redirect you to " . $url);
    }

    /**
     * Calls parent class validate() method.
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

        // @todo necessary?
        if ($this->valid === null) {
            return (bool) $this->sessionSlot['formIsValid'];
        } else {
            return $this->valid;
        }
    }

    /**
     * Retuns if form has been submitted before
     *
     * @return (bool) session status
     **/
    public function isEmpty() {
        if (isset($this->sessionSlot['formName'])) {
            return $this->sessionSlot['formName'] != $this->name;
        } else {
            return true;
        }
    }

    /**
     * Gets input element/fieldset by name.
     *
     * @param $name string - name of the input element we're looking for
     * @return $input object - input element or fieldset
     **/
    public function getElement($name) {
        foreach($this->getElements() as $element) {
            if ($name === $element->getName()) {
                return $element;
            }
        }
        return false;
    }

    /**
    * Gets value of an input element by name.
    * 
    * @param $name name of the input element we're looking for
    * @return (string) or (array) - value of an input element
    **/
    public function getValue($name) {
        return $this->getElement($name)->getValue(); // @todo check if input exists
    }

    /**
     * Allows to manually populate the forms' input elements with values by
     * parsing an array of name-value pairs.
     *
     * @param $data array - contains input element names (key) and desired values (value)
     **/
    public function populate($data = array()) {
        foreach($data as $name => $value) {
            $element = $this->getElement($name);
            if ($element) {
               $element->setDefaultValue($value);
            }
        }
    }

    /**
     * Gets form-data from current PHP session.
     *
     * @return (array) of form-data similar to $_POST
     **/
    public function getValues() {
        return $this->sessionSlot;
    }

    /** 
     * Checks within the form if an input element or fieldset name is already
     * taken. If so, it throws an exception.
     *
     * @param $name name to check
     **/
    public function checkElementName($name) {
        foreach($this->getElements() as $element) {  
            if ($element->getName() === $name) {
                throw new exceptions\duplicateElementNameException();
            }
        }
    }

    /**
     * Deletes the current forms' PHP session data.
     **/
    public function clearSession() {
        unset($_SESSION[$this->sessionSlotName]);
    }

    /**
     * Hook method - to be overridden with custom validation rules, field requested rules etc.
     **/
    protected function onValidate() {
    }
}

