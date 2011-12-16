<?php
/**
 * @file    multiple.php
 * @brief   multiple input element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace depage\htmlform\elements;

use depage\htmlform\abstracts;

/**
 * @brief HTML-multiple-choice input type i.e. checkbox and select.
 *
 * Class for multiple-choice type HTML elements. Has the same return value,
 * regardless of skin type (checkbox or select).
 *
 * @section usage
 *
 * @code
 * <?php
 *     $form = new depage\htmlform\htmlform('myform');
 *
 *     // add multiple-element (checkbox is the default skin)
 *     $form->addMultiple('listOne', array(
 *         'label' => 'Spoken languages',
 *         'list' => array(
 *             'en' => 'English',
 *             'es' => 'Spanish',
 *             'fr' => 'French',
 *         ),
 *     ));
 *
 *     // add a multiple-element with select-skin
 *     $form->addMultiple('listTwo', array(
 *         'label' => 'Spoken languages',
 *         'skin' => 'select',
 *         'list' => array(
 *             'en' => 'English',
 *             'es' => 'Spanish',
 *             'fr' => 'French',
 *         ),
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
class multiple extends abstracts\input {
    // {{{ variables
    /** 
     * @brief Contains list of selectable options.
     **/
    protected $list = array();
    // }}}

    // {{{ __construct()
    /**
     * @brief   multiple class constructor
     *
     * @param   $name       (string)    element name
     * @param   $parameters (array)     element parameters, HTML attributes, validator specs etc.
     * @param   $form       (object)    parent form object
     * @return  void
     **/
    public function __construct($name, $parameters, $form) {
        parent::__construct($name, $parameters, $form);

        $this->list = (isset($parameters['list']) && is_array($parameters['list'])) ? $parameters['list'] : array();
    }
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

        // multiple-choice-elements have values of type array
        $this->defaults['defaultValue'] = array();
        $this->defaults['skin']         = 'checkbox';
    }
    // }}}

    // {{{ htmlList()
    /**
     * @brief   HTML option list rendering
     *
     * Renders HTML - option list part of select/checkbox element. Works
     * recursively in case of select-optgroups. If no parameters are parsed, it
     * uses the list attribute of this element.
     *
     * @param   $options    (array)     list elements and subgroups
     * @param   $value      (array)     values to be marked as selected
     * @return  $list       (string)    rendered options-part of the HTML-select-element
     *
     * @see     __toString()
     **/
    protected function htmlList($options = null, $value = null) {
        if ($value == null)     $value      = $this->htmlValue();
        if ($options == null)   $options    = $this->list;

        $options    = $this->htmlEscape($options);
        $list       = '';

        // select
        if ($this->skin === "select") {
            foreach($options as $index => $option) {
                if (is_array($option)) {
                    $list       .= "<optgroup label=\"{$index}\">" . $this->htmlList($option, $value) . "</optgroup>";
                } else {
                    $selected   = (in_array($index, $value)) ? ' selected' : '';
                    $list       .= "<option value=\"{$index}\"{$selected}>{$option}</option>";
                }
            }
        // checkbox
        } else {
            $inputAttributes = $this->htmlInputAttributes();

            foreach($options as $index => $option) {
                $selected = (is_array($value) && (in_array($index, $value))) ? " checked=\"yes\"" : '';

                $list .= "<span>" .
                    "<label>" .
                        "<input type=\"checkbox\" name=\"{$this->name}[]\"{$inputAttributes} value=\"{$index}\"{$selected}>" .
                        "<span>{$option}</span>" .
                    "</label>" .
                "</span>";
            }
        }
        return $list;
    }
    // }}}

    // {{{ __toString()
    /**
     * @brief   Renders element to HTML.
     *
     * @return  (string) HTML rendered element
     *
     * @see     htmlList()
     **/
    public function __toString() {
        $marker             = $this->htmlMarker();
        $label              = $this->htmlLabel();
        $list               = $this->htmlList();
        $wrapperAttributes  = $this->htmlWrapperAttributes();
        $errorMessage       = $this->htmlErrorMessage();

        if ($this->skin === 'select') {
            // render HTML select

            $inputAttributes = $this->htmlInputAttributes();

            return "<p {$wrapperAttributes}>" .
                "<label>" .
                    "<span class=\"label\">{$label}{$marker}</span>" .
                    "<select multiple name=\"{$this->name}[]\"{$inputAttributes}>{$list}</select>" .
                "</label>" .
                $errorMessage .
            "</p>\n";
        } else {
            // render HTML checkbox

            return "<p {$wrapperAttributes}>" .
                "<span class=\"label\">{$label}{$marker}</span>" .
                "<span>{$list}</span>" .
                $errorMessage .
            "</p>\n";
        }
    }
    // }}}

    // {{{ htmlInputAttributes()
    /**
     * @brief   Returns string of HTML attributes for input element.
     *
     * (overrides parent to avoid awkward HTML5 checkbox validation (all
     * boxes need to be checked if required))
     *
     * @return  $attributes (string) HTML attributes
     **/
    protected function htmlInputAttributes() {
        $attributes = '';

        // HTML5 validator hack
        if ($this->required && $this->skin === 'select') $attributes .= ' required="required"';
        if ($this->autofocus)                            $attributes .= ' autofocus="autofocus"';

        return $attributes;
    }
    // }}}

    // {{{ typeCastValue()
    /**
     * @brief   Converts value to element specific type.
     *
     * @return  void
     **/
    protected function typeCastValue() {
        if ($this->value == "") {
            $this->value = array();
        } else {
            $this->value = (array) $this->value;
        }
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
