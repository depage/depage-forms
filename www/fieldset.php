<?php 

require_once('container.php');

/**
 * The fieldset class holds HTML-fieldset specific attributes and methods.
 **/
class fieldset extends container {
    /**
     * Contains reference to current fieldsets' parent HTML form.
     **/
    protected $form;

    /**
     * sets parent form of fieldset
     *
     *  @param $form object - parent form object
     *  @return void
     **/
    public function setParentForm($form) {
        $this->form = $form;

        $this->addChildElements();
    }

    /**
     * overwritable method to add child elements
     *
     *  @return void
     **/
    protected function addChildElements() {
    }

    /**
     * Specifies default values of fieldset attributes for the parent class
     * constructor.
     *
     * @return void
     **/
    protected function setDefaults() {
        parent::setDefauls();
        $this->defaults['label'] = $this->name;
    }

    /** 
     * Calls parent class to generate an input element or a fieldset and add
     * it to its list of elements
     * 
     * @param $type input type or fieldset
     * @param $name string - name of the element
     * @param $parameters array of element attributes: HTML attributes, validation parameters etc.
     * @return object $newInput
     **/
     public function addElement($type, $name, $parameters = array()) {
        $this->form->checkInputName($name);

        $newElement = parent::addElement($type, $name, $parameters);
        
        if (is_a($newElement, 'fieldset')) {
            // if it's a fieldset it needs to know which form it belongs to
            $newElement->setParentForm($this->form);
        } else {
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
        $renderedElements = '';
        foreach($this->elementsAndHtml as $element) {
            $renderedElements .= $element;
        }
        return "<fieldset id=\"$this->name\" name=\"$this->name\">" .
            "<legend>$this->label</legend>$renderedElements" .
        "</fieldset>\n";
    }
}
