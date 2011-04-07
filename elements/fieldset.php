<?php 

namespace depage\htmlform\elements;

use depage\htmlform\abstracts;

/**
 * The fieldset class holds HTML-fieldset specific attributes and methods.
 **/
class fieldset extends abstracts\container {
    /**
     * Contains reference to current fieldsets' parent HTML form.
     **/
    protected $form;

    /**
     * collects initial values across subclasses.
     **/
    protected function setDefaults() {
        parent::setDefaults();

        $this->defaults['label'] = $this->name;
    }

    /** 
     * Calls parent class to generate an input element or a fieldset and add
     * it to its list of elements
     * 
     * @param $type input element type or fieldset
     * @param $name string - name of the element
     * @param $parameters array of element attributes: HTML attributes, validation parameters etc.
     * @return object $newElement
     **/
     public function addElement($type, $name, $parameters) {
        $this->form->checkElementName($name);

        $newElement = parent::addElement($type, $name, $parameters);

        if ( !($newElement instanceof fieldset) ) {
            $this->form->updateInputValue($name);
        }

        return $newElement;
    }

    /**
     * Renders the fieldset as HTML code. If the fieldset contains elements it
     * calls their rendering methods.
     *
     * @return string
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
}
