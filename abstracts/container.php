<?php 

/**
 * The abstract container class contains the similatrities of the child classes
 * formClass and fieldset.
 * 
 **/

namespace depage\htmlform\abstracts;

use depage\htmlform\elements;
use depage\htmlform\exceptions;

abstract class container extends element {
    /**
     * Holds references to input elements and fieldsets.
     **/
    protected $elements = array();
    /**
     * Holds input element, fieldset and custom HTML object references.
     **/
    protected $elementsAndHtml = array();
    /**
     * Parent form object reference
     **/
    protected $form;

    /**
     * @param $name string - container name
     * @param $parameters array of container parameters, HTML attributes
     * @param $form parent form object.
     * @return void
     **/
    public function __construct($name, $parameters, $form) {
        $this->form = $form;

        parent::__construct($name, $parameters, $form);
    }

    /**
     * Allows for various input element types and fieldsets. Accepts methods
     * that start with "add" and tries to call $this->addElement() to instantiate
     * the respective class.
     * Methods that start with "html" return the respective HTML escaped
     * attributes for element rendering.
     **/
    public function __call($functionName, $functionArguments) {
        if (substr($functionName, 0, 3) === 'add') {
            $type       = strtolower(str_replace('add', '\\depage\\htmlform\\elements\\', $functionName));
            $name       = (isset($functionArguments[0]))    ? $functionArguments[0] : '';
            $parameters = isset($functionArguments[1])      ? $functionArguments[1] : array();

            return $this->addElement($type, $name, $parameters);
        } else {
            return parent::__call($functionName, $functionArguments);
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
    protected function addElement($type, $name, $parameters) {
        $this->_checkElementType($type);

        $parameters['log'] = $this->log;

        $newElement = new $type($name, $parameters, $this->form);

        $this->elements[] = $newElement;
        $this->elementsAndHtml[] = $newElement;

        if ($newElement instanceof container) {
            $newElement->addChildElements();
        }
        return $newElement;
    }

    /**
     * overridable method to add child elements
     *
     * @return void
     **/
    public function addChildElements() {
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
     * Checks if an input type class exists. Throws an exception if it doesn't.
     *
     * @param $type input type to be checked
     **/
    private function _checkElementType($type) {
        if (!class_exists($type)) {
            throw new exceptions\unknownElementTypeException();
        }
    }

    /**
     * Walks recursively through current containers' elements to compile a list
     * of input elements.
     *
     * @return $allElements array of input elements
     **/
    public function getElements($includeFieldsets = false) {
        $allElements = array();
        foreach($this->elements as $element) {
            if ($element instanceof elements\fieldset) {
                $allElements = array_merge($allElements, $element->getElements());
                if ($includeFieldsets) {
                    $allElements[] = $element;
                }
            } else {
                $allElements[] = $element;
            }
        }
        return $allElements;
    }

    /**
     * Gets input/container element by name.
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
}
