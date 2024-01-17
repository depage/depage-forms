<?php
/**
 * @file    multiple.php
 * @brief   multiple input element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace Depage\HtmlForm\Elements;

use Depage\HtmlForm\Abstracts;

/**
 * @brief HTML-multiple-choice input type i.e. checkbox and select.
 *
 * Class for multiple-choice type HTML elements. Has the same return value,
 * regardless of skin type (checkbox or select).
 *
 * @see Depage::HtmlForm::Elements::Single
 * @see Depage::HtmlForm::Elements::Boolean
 *
 * Usage
 * -----
 *
 * @code
    <?php
        $form = new Depage\HtmlForm\HtmlForm('myform');

        // add multiple-element (checkbox is the default skin)
        $form->addMultiple('listOne', array(
            'label' => 'Spoken languages',
            'list' => array(
                'en' => 'English',
                'es' => 'Spanish',
                'fr' => 'French',
            ),
        ));

        // add a multiple-element with select-skin
        $form->addMultiple('listTwo', array(
            'label' => 'Spoken languages',
            'skin' => 'select',
            'list' => array(
                'en' => 'English',
                'es' => 'Spanish',
                'fr' => 'French',
            ),
        ));

        // process form
        $form->process();

        // Display the form.
        echo ($form);
    @endcode
 **/
class Multiple extends Abstracts\Input
{
    // {{{ variables
    /**
     * @brief Contains list of selectable options.
     **/
    protected $list = array();

    /**
     * @brief HTML skin type (checkbox or select).
     **/
    protected $skin = 'radio';

    /**
     * @brief maxItems
     **/
    protected $maxItems = null;
    // }}}

    // {{{ __construct()
    /**
     * @brief   multiple class constructor
     *
     * @param  string $name       element name
     * @param  array  $parameters element parameters, HTML attributes, validator specs etc.
     * @param  object $form       parent form object
     * @return void
     **/
    public function __construct($name, $parameters, $form)
    {
        parent::__construct($name, $parameters, $form);

        $this->list = (isset($parameters['list']) && is_array($parameters['list'])) ? $parameters['list'] : array();
        $this->maxItems = isset($parameters['maxItems']) ? $parameters['maxItems'] : $this->maxItems;
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
     * @return void
     **/
    protected function setDefaults()
    {
        parent::setDefaults();

        // multiple-choice-elements have values of type array
        $this->defaults['defaultValue'] = array();
        $this->defaults['skin']         = 'checkbox';
        $this->defaults['maxItems']     = null;
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
     * @param  array  $options list elements and subgroups
     * @param  array  $value   values to be marked as selected
     * @return string $list       rendered options-part of the HTML-select-element
     *
     * @see     __toString()
     **/
    protected function htmlList($options = null, $value = null)
    {
        if ($value == null)     $value      = $this->htmlValue();
        if ($options == null)   $options    = $this->list;

        $options    = $this->htmlEscape($options);
        $list       = '';

        // select
        if (in_array($this->skin, ['select', 'tags'])) {
            foreach ($options as $index => $option) {
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

            foreach ($options as $index => $option) {
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
     * @return string HTML rendered element
     *
     * @see     htmlList()
     **/
    public function __toString()
    {
        $marker             = $this->htmlMarker();
        $label              = $this->htmlLabel();
        $list               = $this->htmlList();
        $wrapperAttributes  = $this->htmlWrapperAttributes();
        $errorMessage       = $this->htmlErrorMessage();
        $helpMessage        = $this->htmlHelpMessage();

        if (in_array($this->skin, ['select', 'tags'])) {
            // render HTML select

            $inputAttributes = $this->htmlInputAttributes();

            return "<p {$wrapperAttributes}>" .
                "<label>" .
                    "<span class=\"depage-label\">{$label}{$marker}</span>" .
                    "<select multiple name=\"{$this->name}[]\"{$inputAttributes}>{$list}</select>" .
                "</label>" .
                $errorMessage .
                $helpMessage .
            "</p>\n";
        } else {
            // render HTML checkbox
            return "<p {$wrapperAttributes}>" .
                "<span class=\"depage-label\">{$label}{$marker}</span>" .
                "<span>{$list}</span>" .
                $errorMessage .
                $helpMessage .
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
     * @return string $attributes HTML attributes
     **/
    protected function htmlInputAttributes()
    {
        $attributes = '';

        // HTML5 validator hack
        if ($this->required && in_array($this->skin, ['select', 'tags'])) $attributes .= ' required="required"';
        if ($this->maxItems)                                              $attributes .= " data-max-items=\"$this->maxItems\"";
        return $attributes;
    }
    // }}}

    // {{{ typeCastValue()
    /**
     * @brief   Converts value to element specific type.
     *
     * @return void
     **/
    protected function typeCastValue()
    {
        if ($this->value == "") {
            $this->value = array();
        } else {
            $this->value = (array) $this->value;

            if ($this->maxItems) {
                $this->value = array_slice($this->value, 0, $this->maxItems);
            }
        }
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
