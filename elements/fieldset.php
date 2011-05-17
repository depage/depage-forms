<?php
/**
 * @file    fieldset.php
 * @brief   fieldset container element
 **/

namespace depage\htmlform\elements;

use depage\htmlform\abstracts;

/**
 * @brief The fieldset class holds HTML-fieldset specific attributes and methods.
 **/
class fieldset extends abstracts\container {
    // {{{ variables
    /**
     * @brief parent HTML form.
     **/
    protected $form;
    // }}}

    // {{{ setDefaults()
    /**
     * @brief collects initial values across subclasses.
     **/
    protected function setDefaults() {
        parent::setDefaults();

        $this->defaults['label'] = $this->name;
    }
    // }}}

    // {{{ addElement()
    /**
     * @brief Generates sub-elements.
     *
     * Calls parent class to generate an input element or a fieldset and add
     * it to its list of elements
     *
     * @param   $type       (string) elememt type
     * @param   $name       (string) element name
     * @param   $parameters (array)  element attributes: HTML attributes, validation parameters etc.
     * @return  $newElement (object) new element object
     *
     * @see     __call()
     * @see     addChildElements()
     **/
     public function addElement($type, $name, $parameters) {
        $this->form->checkElementName($name);

        $newElement = parent::addElement($type, $name, $parameters);

        if ( !($newElement instanceof fieldset) ) {
            $this->form->updateInputValue($name);
        }

        return $newElement;
    }
    // }}}

    // {{{ __toString()
    /**
     * @brief   Renders the fieldset to HTML code.
     *
     * If the fieldset contains subelements it calls their rendering methods.
     *
     * @return  (string) HTML-rendered fieldset
     **/
     public function __toString() {
        $renderedElements   = '';
        $formName           = $this->form->getName();
        $label              = $this->htmlLabel();

        foreach($this->elementsAndHtml as $element) {
            $renderedElements .= $element;
        }

        return "<fieldset id=\"{$formName}-{$this->name}\" name=\"{$this->name}\">" .
            "<legend>{$label}</legend>{$renderedElements}" .
        "</fieldset>\n";
    }
    // }}}
}
