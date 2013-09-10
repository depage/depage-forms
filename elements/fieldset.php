<?php
/**
 * @file    fieldset.php
 * @brief   fieldset container element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace depage\htmlform\elements;

use depage\htmlform\abstracts;

/**
 * @brief The fieldset class holds HTML-fieldset specific attributes and methods.
 *
 * Class for the HTML fieldset element. Can be used to group other elments
 * together.
 *
 * @section usage
 *
 * @code
 * <?php
 *     $form = new depage\htmlform\htmlform('myform');
 *
 *     // add a fieldset container
 *     $myfieldset = $form->addFieldset('myfieldset', array(
 *         'label' => 'Name',
 *     ));
 *
 *     // add a text field
 *     $myfieldset->addText('firstName', array(
 *         'label' => 'First name',
 *     ));
 *
 *     // add another text field
 *     $myfieldset->addText('lastName', array(
 *         'label' => 'Last name',
 *     ));
 *
 *     // process form
 *     $form->process();
 *
 *     // Display the form.
 *     echo ($form);
 * ?>
 * @endcode
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
     * @brief   collects initial values across subclasses.
     *
     * The constructor loops through these and creates settable class
     * attributes at runtime. It's a compact mechanism for initialising
     * a lot of variables.
     *
     * @return  void
     **/
    protected function setDefaults() {
        parent::setDefaults();

        $this->defaults['label']    = $this->name;
        $this->defaults['disabled'] = false;
        $this->defaults['required'] = false;
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

    // {{{ htmlClasses()
    /**
     * @brief   Returns string of the elements' HTML-classes, separated by spaces.
     *
     * @return  $classes (string) HTML-classes
     **/
    protected function htmlClasses() {
        $classes = '';

        if ($this->required) {
            $classes .= ' required';
        }
        if ($this->disabled) {
            $classes .= ' disabled';
        }
        if (!empty($this->class)) {
            $classes .= ' ' . $this->htmlEscape($this->class);
        }

        return trim($classes);
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
        $classes            = $this->htmlClasses();

        $htmlAttributes     = "id=\"{$formName}-{$this->name}\"";
        $htmlAttributes     .= " name=\"{$this->name}\"";

        if (!empty($classes)) {
            $htmlAttributes     .= " class=\"" . $classes . "\"";
        }

        foreach($this->elementsAndHtml as $element) {
            $renderedElements .= $element;
        }

        return "<fieldset {$htmlAttributes}>" .
            "<legend>{$label}</legend>{$renderedElements}" .
        "</fieldset>\n";
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
