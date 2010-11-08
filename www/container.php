<?php 

require_once('exceptions.php');
require_once('fieldset.php');
require_once('formClass.php');
require_once('html.php');

/**
 * The abstract container class contains the similatrities of the child classes
 * formClass and fieldset.
 * 
 **/
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
     * Holds references to input elements and fieldsets.
     **/
    protected $elements = array();
    /**
     * Holds input element, fieldset and custom HTML object references.
     **/
    protected $elementsAndHtml = array();
    /**
     * Contains default attribute values.
     **/
    protected $defaults = array();

    /**
     *  @param $name string - container name
     *  @param $parameters array of container parameters, HTML attributes
     *  @return void
     **/
    public function __construct($name, $parameters = array()) {
        $this->checkContainerName($name);
        $this->name = $name;

        // loads default attributes from $this->defaults array
        $this->setDefaults();
        foreach ($this->defaults as $parameter => $default) {
            $this->$parameter = isset($parameters[$parameter]) ? $parameters[$parameter] : $default;
        }
    }

    /**
     * Hook method to be overridden by children in order to specify default
     * values.
     **/
    protected function setDefaults() {
    }

    /**
     * Allows for various input element types and fieldsets. Accepts methods
     * that start with "add" and tries to call $this->addElement() to instantiate
     * the respective class.
     *
     * @param $name string - name of the element
     * @param $parameters array of element attributes: HTML attributes, validation parameters etc.
     **/
    public function __call($functionName, $functionArguments) {
        if (substr($functionName, 0, 3) === 'add') {
            $type = strtolower(str_replace('add', '', $functionName));
            $name = (isset($functionArguments[0])) ? $functionArguments[0] : '';
            $parameters = isset($functionArguments[1]) ? $functionArguments[1] : array();
            return $this->addElement($type, $name, $parameters);
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
    public function addElement($type, $name, $parameters = array()) {
        $this->_checkInputType($type);

        $newElement = new $type($name, $parameters, $this->name);

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
        if (!is_string($htmlString)) {
            throw new htmlNoStringException();
        }

        $newHtml = new html($htmlString);
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
        $this->valid = true;
        foreach($this->elements as $element) {
            $element->validate();
            $this->valid = (($this->valid) && ($element->isValid()));
        }
    }

    /**
     * Returns current containers' validation status.
     *
     * @return $this->valid
     **/
    public function isValid() {
        return $this->valid;
    }

    /**
     * Sets current containers' elements' required-HTML attribute recursively
     * to true.
     *
     * @return void
     **/
    public function setRequired() {
        foreach ($this->elements as $element) {
            $element->setRequired();
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
            throw new containerNameNoStringException();
        }
        if (trim($name) === '') {
            throw new invalidContainerNameException();
        }
    }

    /** 
     * Checks if an input type class exists. Throws an exception if it doesn't.
     *
     * @param $type input type to be checked
     **/
    private function _checkInputType($type) {
        if (!class_exists($type)) {
            throw new unknownInputTypeException();
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
     * @return $allInputs array of input elements
     **/
    public function getElements() {
        $allElements = array();
        foreach($this->elements as $element) {  
            if (is_a($element, 'fieldset')) {
                $allElements = array_merge($allElements, $element->getElements());
            } else {
                $allElements[] = $element;
            }
        }
        return $allElements;
    }
}
