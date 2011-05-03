<?php 
/**
 * @file container.php
 * @brief abstract container element class
 **/

namespace depage\htmlform\abstracts;

use depage\htmlform\elements;
use depage\htmlform\exceptions;

/**
 * @brief container element base class
 *
 * The abstract container class contains the base for container type elements.
 * ie. htmlform, fieldset and step
 **/
abstract class container extends element {
    /**
     * @brief References to input elements and fieldsets.
     **/
    protected $elements = array();
    /**
     * @brief Input element, fieldset and custom HTML object references.
     **/
    protected $elementsAndHtml = array();
    /**
     * @brief Parent form object reference
     **/
    protected $form;

    /**
     * @brief   container class constructor
     *
     * @param   $name           (string)    container name
     * @param   $parameters     (array)     container parameters, HTML attributes
     * @param   $form           (object)    parent form object
     * @return  void
     **/
    public function __construct($name, $parameters, $form) {
        $this->form = $form;

        parent::__construct($name, $parameters, $form);
    }

    /**
     * @brief   HTML escaping and add subelements
     *
     * -# (Methods beginning with "add") Calls addElement() with
     *    element-specific type argument. Tries to instantiate the respective
     *    class.
     * -# (Methods beginnning with "html)" Returns the respective HTML escaped
     *    attributes for element rendering.
     *
     * @param   $function   (string)    function name
     * @param   $arguments  (array)     function arguments
     * @return              (object)    element object or (mixed) HTML escaped value
     *
     * @see     addElement()
     * @see     htmlEscape()
     **/
    public function __call($function, $arguments) {
        if (substr($function, 0, 3) === 'add') {
            $type       = strtolower(str_replace('add', '\\depage\\htmlform\\elements\\', $function));
            $name       = isset($arguments[0])  ? $arguments[0] : '';
            $parameters = isset($arguments[1])  ? $arguments[1] : array();

            return $this->addElement($type, $name, $parameters);
        } else {
            return parent::__call($function, $arguments);
        }
    }

    /**
     * @brief Generates sub-elements.
     * 
     * Adds new child elements to $this->elements.
     *
     * @param   $type       (string) elememt type
     * @param   $name       (string) element name
     * @param   $parameters (array)  element attributes: HTML attributes, validation parameters etc.
     * @return  $newElement (object) new element object
     *
     * @see     __call()
     * @see     addChildElements()
     **/
    protected function addElement($type, $name, $parameters) {
        $this->_checkElementType($type);
        $this->checkParameters($parameters);

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
     * @brief   Sub-element generator hook
     *
     * Adds child elements just after container construction.
     *
     * @return  void
     *
     * @see     addElement()
     **/
    public function addChildElements() {}

    /**
     * @brief   Adds a new custom HTML element to the container.
     *
     * @param   $html           (string) HTML code
     * @return  $htmlElement    (object) HTML element object
     *
     * @see     depage::htmlform::elements::html
     **/
    public function addHtml($html) {
        $htmlElement = new elements\html($html);

        $this->elementsAndHtml[] = $htmlElement;

        return $htmlElement;
    }

    /**
     * @brief Validates container and its contents.
     *
     * Walks recursively through current containers' elements and checks if
     * they're valid. Saves the result to $this->valid.
     *
     * @return $this->valid (bool) validation result
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
     * @brief   Sets required-attribute
     *
     * Sets current containers' elements' HTML-required attribute recursively.
     *
     * @param   $required (bool) HTML-required attribute
     * @return  void
     **/
    public function setRequired($required = true) {
        $required = (bool) $required;

        foreach ($this->elements as $element) {
            $element->setRequired($required);
        }
    }

    /**
     * @brief   Checks element class availability
     *
     * Checks if an element type class exists. Throws an exception if it doesn't.
     *
     * @param   $type (string) element type
     * @return  void
     **/
    private function _checkElementType($type) {
        if (!class_exists($type)) {
            throw new exceptions\unknownElementTypeException();
        }
    }

    /**
     * @brief Returns containers subelements.
     *
     * Walks recursively through current containers' elements to compile a list
     * of subelements.
     *
     * @param   $includeFieldsets   (bool)  include fieldset/step elements in result
     * @return  $allElements        (array) subelements
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
     * @brief   Gets subelement by name.
     *
     * Searches subelements by name and returns a single element object if
     * successful. Returns false if the element can't be found.
     *
     * @param   $name       (string) name of the input element we're looking for
     * @return  $element    (object) subelement or (bool) false if unsuccessful
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
