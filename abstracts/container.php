<?php 

/**
 * The abstract container class contains the similatrities of the child classes
 * formClass and fieldset.
 * 
 **/

namespace depage\htmlform\abstracts;

use depage\htmlform\elements;
use depage\htmlform\exceptions;

abstract class container {
    /**
     * Name of the container.
     **/
    protected $name;
    /**
     * Is true if all container elements are valid, false if one of them isn't.
     **/
    protected $valid;
    /**
     * Container validation result/status.
     **/
    protected $validated = false;
    /**
     * Holds references to input elements and fieldsets.
     **/
    protected $elements = array();
    /**
     * Holds input element, fieldset and custom HTML object references.
     **/
    protected $elementsAndHtml = array();

    /**
     *  @param $name string - container name
     *  @param $parameters array of container parameters, HTML attributes
     *  @return void
     **/
    public function __construct($name, &$parameters = array()) {
        // converts index to lower case so parameters are case independent
        $parameters = array_change_key_case($parameters);

        $this->log = (isset($parameters['log'])) ? $parameters['log'] : null;
        $this->checkContainerName($name);
        $this->name = $name;
    }

    /**
     * Allows for various input element types and fieldsets. Accepts methods
     * that start with "add" and tries to call $this->addElement() to instantiate
     * the respective class.
     * Methods that start with "html" return the respective HTML escaped
     * attributes for element rendering.
     **/
    public function __call($functionName, $functionArguments) {
        if (substr($functionName, 0, 4) === 'html') {
            $attribute = str_replace('html', '', $functionName);
            $attribute{0} = strtolower($attribute{0});

            return htmlentities($this->$attribute, ENT_QUOTES);
        } else if (substr($functionName, 0, 3) === 'add') {
            $type       = strtolower(str_replace('add', '\\depage\\htmlform\\elements\\', $functionName));
            $name       = (isset($functionArguments[0]))    ? $functionArguments[0] : '';
            $parameters = isset($functionArguments[1])      ? $functionArguments[1] : array();

            return $this->addElement($type, $name, $parameters);
        } else {
            trigger_error("Call to undefined method $functionName", E_USER_ERROR);
        }
    }

    /**
     * Generates elements and adds their references to $this->$elements.
     * 
     * @param $type input type or fieldset
     * @param $name string - name of the element
     * @param $parameters array of element attributes: HTML attributes, validation parameters etc.
     * @return $newElement
     **/
    protected function addElement($type, $name, $parameters = array()) {
        $this->_checkElementType($type);

        $parameters['log'] = $this->log;

        $formName = (isset($this->form)) ? $this->form->getName() : $this->name;
        $newElement = new $type($name, $parameters, $formName);

        $this->elements[] = $newElement;
        $this->elementsAndHtml[] = $newElement;

        return $newElement;
    }

    /**
     * Adds a custom HTML element to the form.
     *
     * @param $htmlString
     * return $newHtml custom HTML element reference
     **/
    public function addHtml($htmlString) {
        $newHtml = new elements\html($htmlString);

        $this->elementsAndHtml[] = $newHtml;
        
        return $newHtml;
    }

    /**
     * Walks recursively through current containers' elements and checks if
     * they're valid. Saves the outcome to $this->valid.
     *
     * @return void
     **/
    public function validate() {
        if (!$this->validated) {
            $this->validated = true;

            $this->valid = true;
            foreach($this->elements as $element) {
                $element->validate();
                $this->valid = (($this->valid) && ($element->validate()));
            }
        }
        return $this->valid;
    }

    /**
     * Sets current containers' elements' required-HTML attribute recursively.
     *
     * @return void
     **/
    public function setRequired($required) {
        $required = (bool) $required;

        foreach ($this->elements as $element) {
            $element->setRequired($required);
        }
    }

    /**
     * Checks if desired container name is a valid string. Throws an exception
     * if it isn't.
     *
     * @param $name container name to be checked
     **/
    protected function checkContainerName($name) {
        if (!is_string($name)) {
            throw new exceptions\containerNameNoStringException();
        }
        if ((trim($name) === '') || preg_match('/[^a-zA-Z0-9_]/', $name)) {
            throw new exceptions\invalidContainerNameException();
        }
    }

    /** 
     * Checks if an input type class exists. Throws an exception if it doesn't.
     *
     * @param $type input type to be checked
     **/
    private function _checkElementType($type) {
        if (!class_exists($type)) {
            throw new exceptions\unknownInputTypeException();
        }
    }

    /**
     * Returns current containers name.
     *
     * @return $this->name
     **/
    public function getName() {
        return $this->name;
    }

    /**
     * Walks recursively through current containers' elements to compile a list
     * of input elements.
     *
     * @return $allElements array of input elements
     **/
    public function getElements() {
        $allElements = array();
        foreach($this->elements as $element) {  
            if ($element instanceof elements\fieldset) {
                $allElements = array_merge($allElements, $element->getElements());
            } else {
                $allElements[] = $element;
            }
        }
        return $allElements;
    }

    protected function log($argument, $type) {
        if (is_callable(array($this->log, 'log'))) {
            $this->log->log($argument, $type);
        } else {
            error_log($argument);
        }
    }
}
