<?php 

require_once('container.php');

/**
 * The fieldset class holds HTML-fieldset specific attributes and methods.
 **/
class fieldset extends container {
    /**
     * Contains reference to current fieldsets' parent HTML form.
     **/
    private $form;

     /**
     *  @param $name string - fieldset name
     *  @param $parameters array of fieldset parameters, HTML attributes
     *  @return void
     **/
    public function __construct($name, $parameters = array()) {
        parent::__construct($name, $parameters);

        $this->form = $parameters['form'];
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

        // if it's a fieldset it needs to know which form it belongs to
        if ($type === 'fieldset') {
            $parameters['form'] = $this->form;
        }

        $newElement = parent::addElement($type, $name, $parameters);
        
        if ($type !== 'fieldset') {
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
