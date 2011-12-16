<?php
/**
 * @file    text.php
 * @brief   text input element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace depage\htmlform\elements;

use depage\htmlform\abstracts;

/**
 * @brief HTML text input type.
 *
 * Class for the HTML input-type "text".
 *
 * @section usage
 *
 * @code
 * <?php
 *     $form = new depage\htmlform\htmlform('myform');
 *
 *     // add a text field
 *     $form->addText('food', array(
 *         'label' => 'Favourite food',
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
class text extends abstracts\input {
    // {{{ variables
    /**
     * @brief HTML placeholder attribute
     **/
    protected $placeholder;
    // }}}

    // {{{ __construct()
    /**
     * @brief   text class constructor
     *
     * @param   $name       (string)    element name
     * @param   $parameters (array)     element parameters, HTML attributes, validator specs etc.
     * @param   $form       (object)    parent form object
     * @return  void
     **/
    public function __construct($name, $parameters, $form) {
        parent::__construct($name, $parameters, $form);

        $this->list = (isset($parameters['list']) && is_array($parameters['list'])) ? $parameters['list'] : false;
    }
    // }}}

    // {{{ setDefaults()
    /**
     * @brief   collects initial values across subclasses
     *
     * The constructor loops through these and creates settable class
     * attributes at runtime. It's a compact mechanism for initialising
     * a lot of variables.
     *
     * @return  void
     **/
    protected function setDefaults() {
        parent::setDefaults();

        // textClass elements have values of type string
        $this->defaults['defaultValue'] = '';
        $this->defaults['placeholder']  = false;
    }
    // }}}

    // {{{ __toString()
    /**
     * @brief   Renders element to HTML.
     *
     * @return  (string) HTML rendered element
     **/
    public function __toString() {
        $value              = $this->htmlValue();
        $inputAttributes    = $this->htmlInputAttributes();
        $marker             = $this->htmlMarker();
        $label              = $this->htmlLabel();
        $list               = $this->htmlList();
        $wrapperAttributes  = $this->htmlWrapperAttributes();
        $errorMessage       = $this->htmlErrorMessage();

        return "<p {$wrapperAttributes}>" .
            "<label>" .
                "<span class=\"label\">{$label}{$marker}</span>" .
                "<input name=\"{$this->name}\" type=\"{$this->type}\"{$inputAttributes} value=\"{$value}\">" .
                $list .
            "</label>" .
            $errorMessage .
        "</p>\n";
    }
    // }}}

    // {{{ htmlList()
    /**
     * @brief   Renders HTML datalist
     *
     * @param   $options    (array)     datalist
     * @return  $htmlList   (string)    rendered HTML datalist
     **/
    protected function htmlList($options = null) {
        if ($this->list && is_array($this->list)) {
            $formName   = $this->htmlFormName();
            $options    = $this->htmlEscape($this->list);

            $htmlList = "<datalist id=\"{$formName}-{$this->name}-list\">";

            foreach ($options as $index => $option) {
                // associative arrays have index as value
                if (is_int($index)) {
                    $htmlList .= "<option value=\"{$option}\">";
                } else {
                    $htmlList .= "<option value=\"{$index}\" label=\"{$option}\">";
                }
            }

            $htmlList .= "</datalist>";
        } else {
            $htmlList = "";
        }
        return $htmlList;
    }
    // }}}

    // {{{ htmlInputAttributes()
    /**
     * @brief renders text element specific HTML attributes
     *
     * @return $attributes (string) rendered HTML attributes
     **/
    protected function htmlInputAttributes() {
        $attributes = parent::htmlInputAttributes();

        if ($this->placeholder) $attributes .= " placeholder=\"{$this->placeholder}\"";
        if ($this->list)        $attributes .= " list=\"{$this->formName}-{$this->name}-list\"";

        $attributes .= $this->validator->getPatternAttribute();

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
        $this->value = (string) $this->value;
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
