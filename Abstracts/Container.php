<?php

/**
 * @file container.php
 * @brief abstract container element class
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace Depage\HtmlForm\Abstracts;

use Depage\HtmlForm\Elements;
use Depage\HtmlForm\Exceptions;

/**
 * @brief container element base class
 *
 * The abstract container class contains the base for container type elements.
 * ie. htmlform, fieldset and step
 *
 * @method string addAddress($name, $parameters = array()) Adds a new address fieldset to the container.
 * @method string addBoolean($name, $parameters = array()) Adds a new boolean input element to the container.
 * @method string addButton($name, $parameters = array()) Adds a new button input element to the container.
 * @method string addCaptcha($name, $parameters = array()) Adds a new captcha input element to the container.
 * @method string addColor($name, $parameters = array()) Adds a new color input element to the container.
 * @method string addCreditcard($name, $parameters = array()) Adds a new creditcard fieldset to the container.
 * @method string addDate($name, $parameters = array()) Adds a new date input element to the container.
 * @method string addDatetime($name, $parameters = array()) Adds a new datetime input element to the container.
 * @method string addEmail($name, $parameters = array()) Adds a new email input element to the container.
 * @method string addFieldset($name, $parameters = array()) Adds a new fieldset container to the container.
 * @method string addFile($name, $parameters = array()) Adds a new file input element to the container.
 * @method string addHidden($name, $parameters = array()) Adds a new hidden input element to the container.
 * @method string addMonth($name, $parameters = array()) Adds a new month input element to the container.
 * @method string addMultiple($name, $parameters = array()) Adds a new multiple
 * @method string addNumber($name, $parameters = array()) Adds a new number input element to the container.
 * @method string addPassword($name, $parameters = array()) Adds a new password
 * @method string addPlaceholder($name, $parameters = array()) Adds a new placeholder input element to the container.
 * @method string addPlaceholderMultiple($name, $parameters = array()) Adds a new multiple placeholder input element to the container
 * @method string addRange($name, $parameters = array()) Adds a new range input element to the container.
 * @method string addRichtext($name, $parameters = array()) Adds a new richtext input element to the container.
 * @method string addSearch($name, $parameters = array()) Adds a new search input element to the container.
 * @method string addSingle($name, $parameters = array()) Adds a new single choice input element to the container.
 * @method string addState($name, $parameters = array()) Adds a new state input element to the container.
 * @method string addStep($name, $parameters = array()) Adds a new step container
 * @method string addTel($name, $parameters = array()) Adds a new telephone input element to the container.
 * @method string addText($name, $parameters = array()) Adds a new text input element to the container.
 * @method string addTextarea($name, $parameters = array()) Adds a new textarea input
 * @method string addTime($name, $parameters = array()) Adds a new time input element to the container.
 * @method string addUrl($name, $parameters = array()) Adds a new URL input element to the container.
 * @method string addWeek($name, $parameters = array()) Adds a new week input element to the container.
 **/
abstract class Container extends Element
{
    // {{{ variables
    /**
     * @brief References to input elements and fieldsets.
     **/
    protected $elements = [];
    /**
     * @brief Input element, fieldset and custom HTML object references.
     **/
    protected $elementsAndHtml = [];
    /**
     * @brief Parent form object reference
     **/
    protected $form;
    // }}}
    // {{{ __construct()
    /**
     * @brief   container class constructor
     *
     * @param  string $name       container name
     * @param  array  $parameters container parameters, HTML attributes
     * @param  object $form       parent form object
     * @return void
     **/
    public function __construct(string $name, array $parameters, object $form)
    {
        $this->form = $form;

        parent::__construct($name, $parameters, $form);
    }
    // }}}

    // {{{ __call()
    /**
     * @brief   HTML escaping and add subelements
     *
     * -# (Methods beginning with "add") Calls addElement() with
     *    element-specific type argument. Tries to instantiate the respective
     *    class.
     * -# (Methods beginnning with "html)" Returns the respective HTML escaped
     *    attributes for element rendering.
     *
     * @param  string $function  function name
     * @param  array  $arguments function arguments
     * @return object element object or (mixed) HTML escaped value
     *
     * @see     addElement()
     * @see     htmlEscape()
     **/
    public function __call(string $function, array $arguments): mixed
    {
        if (substr($function, 0, 3) === 'add') {
            $type = str_replace('add', '', $function);

            foreach ($this->form->getNamespaces() as $namespace) {
                $class = $namespace . '\\' . $type;
                if (class_exists($class)) {
                    $name = isset($arguments[0]) ? $arguments[0] : '';
                    $parameters = isset($arguments[1]) ? $arguments[1] : [];
                    return $this->addElement($class, $name, $parameters);
                }
            }

            throw new Exceptions\UnknownElementTypeException($type);
        } else {
            return parent::__call($function, $arguments);
        }
    }
    // }}}

    // {{{ addElement()
    /**
     * @brief Generates sub-elements.
     *
     * Adds new child elements to $this->elements.
     *
     * @param  string $type       elememt type
     * @param  string $name       element name
     * @param  array  $parameters element attributes: HTML attributes, validation parameters etc.
     * @return object $newElement     new element object
     *
     * @see     __call()
     * @see     addChildElements()
     **/
    protected function addElement(string $type, string $name, array $parameters): object
    {
        $parameters['log'] = $this->log;

        $newElement = new $type($name, $parameters, $this->form);

        $this->elements[] = $newElement;
        $this->elementsAndHtml[] = $newElement;

        if ($newElement instanceof container) {
            $newElement->addChildElements();
        }

        return $newElement;
    }
    // }}}
    // {{{ addChildElements()
    /**
     * @brief   Sub-element generator hook
     *
     * Adds child elements just after container construction.
     * Can be used for subclasses of the container class to add
     * new elements to itself.
     *
     * (see @link Depage::HtmlForm::Elements::Creditcard @endlink for an example implementation)
     *
     * @return void
     *
     * @see     addElement()
     **/
    public function addChildElements(): void {}
    // }}}
    // {{{ getElements()
    /**
     * @brief Returns containers subelements.
     *
     * Walks recursively through current containers' elements to compile a list
     * of subelements.
     *
     * @param  bool  $includeFieldsets include fieldset/step elements in result
     * @return array $allElements       subelements
     **/
    public function getElements(bool $includeFieldsets = false): array
    {
        $allElements = [];
        foreach ($this->elements as $element) {
            if ($element instanceof Container) {
                if ($includeFieldsets) {
                    $allElements[] = $element;
                }
                $allElements = array_merge($allElements, $element->getElements($includeFieldsets));
            } else {
                $allElements[] = $element;
            }
        }

        return $allElements;
    }
    // }}}
    // {{{ getElement()
    /**
     * @brief   Gets subelement by name.
     *
     * Searches subelements by name and returns a single element object if
     * successful. Returns false if the element can't be found.
     *
     * @param  string $name name of the input element we're looking for
     * @return object $element    subelement or (bool) false if unsuccessful
     **/
    public function getElement(string $name, bool $includeFieldsets = false): object|bool
    {
        foreach ($this->getElements($includeFieldsets) as $element) {
            if ($name === $element->getName()) {
                return $element;
            }
        }

        return false;
    }
    // }}}

    // {{{ addHtml()
    /**
     * @brief   Adds a new custom HTML element to the container.
     *
     * @param  string $html HTML code
     * @return object $htmlElement    HTML element object
     *
     * @see     Depage::HtmlForm::Elements::Html
     **/
    public function addHtml(string $html): object
    {
        $htmlElement = new Elements\Html($html);

        $this->elementsAndHtml[] = $htmlElement;

        return $htmlElement;
    }
    // }}}
    // {{{ addStepNav()
    /**
     * @brief   Adds automatic step navigation to output
     *
     * @param array $parameter array of opional parameters
     **/
    public function addStepNav(array $parameter = []): object
    {
        $htmlElement = new Elements\Stepnav($parameter, $this->form);

        $this->elementsAndHtml[] = $htmlElement;

        return $htmlElement;
    }
    // }}}

    // {{{ setRequired()
    /**
     * @brief   Sets required-attribute
     *
     * Sets current containers' elements' HTML-required attribute recursively.
     *
     * @param  bool $required HTML-required attribute
     * @return void
     **/
    public function setRequired(bool $required = true): void
    {
        $required = (bool) $required;

        foreach ($this->elements as $element) {
            $element->setRequired($required);
        }
    }
    // }}}
    // {{{ validate()
    /**
     * @brief Validates container and its contents.
     *
     * Walks recursively through current containers' elements and checks if
     * they're valid. Saves the result to $this->valid.
     *
     * @return bool $this->valid validation result
     **/
    public function validate(): bool
    {
        if (!$this->validated) {
            $this->validated = true;

            $this->valid = true;
            foreach ($this->elements as $element) {
                $valid = $element->validate();
                $this->valid = $this->valid && $valid;
            }
        }

        return $this->valid;
    }
    // }}}
    // {{{ clearValue()
    /**
     * @brief   Deletes values of all child elements
     *
     * @return void
     **/
    public function clearValue(): void
    {
        foreach ($this->getElements(true) as $element) {
            $element->clearValue();
        }
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
