<?php

/**
 * @file    single.php
 * @brief   single input element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace Depage\HtmlForm\Elements;

use Depage\HtmlForm\Abstracts;

/**
 * @brief HTML-single-choice input type i.e. radio and select.
 *
 * Class for radio-like HTML elements. Has the same return value, regardless
 * of skin type (radio or select).
 *
 * Usage
 * -----
 *
 * @code
    <?php
        $form = new Depage\HtmlForm\HtmlForm('myform');

        // add single-element (radio is the default skin)
        $form->addSingle('listOne', array(
            'label' => 'Language',
            'list' => array(
                'en' => 'English',
                'es' => 'Spanish',
                'fr' => 'French',
            ),
        ));

        // add a single-element with select-skin
        $form->addSingle('listTwo', array(
            'label' => 'Language',
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
class Single extends Abstracts\Input
{
    // {{{ variables
    /**
     * @brief Contains list of selectable options.
     **/
    protected $list = [];

    /**
     * @brief HTML skin type (radio or select).
     **/
    protected $skin = 'radio';
    // }}}

    // {{{ __construct()
    /**
     * @brief   single class constructor
     *
     * @param  string $name       element name
     * @param  array  $parameters element parameters, HTML attributes, validator specs etc.
     * @param  object $form       parent form object
     * @return void
     **/
    public function __construct(string $name, array $parameters, object $form)
    {
        parent::__construct($name, $parameters, $form);

        $this->list = (isset($parameters['list']) && is_array($parameters['list'])) ? $parameters['list'] : [];
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
     * @return void
     **/
    protected function setDefaults(): void
    {
        parent::setDefaults();

        // single-choice-elements have values of type string
        $this->defaults['defaultValue'] = '';
        $this->defaults['skin']         = 'radio';
    }
    // }}}

    // {{{ htmlList()
    /**
     * @brief   Renders HTML - option list part of select/radio single element
     *
     * Works recursively in case of select-optgroups. If no parameters are
     * parsed, it uses the list attribute of this element.
     *
     * @param  array  $options list elements and subgroups
     * @param  string $value   value to be marked as selected
     * @return string $list       options-part of the HTML-select-element
     **/
    protected function htmlList(?array $options = null, ?string $value = null): string
    {
        if ($value == null) {
            $value      = $this->htmlValue();
        }
        if ($options == null) {
            $options    = $this->list;
        }

        $options    = $this->htmlEscape($options);
        $list       = '';

        if ($this->skin === "select") {
            foreach ($options as $index => $option) {
                if (is_array($option)) {
                    $list       .= "<optgroup label=\"{$index}\">" . $this->htmlList($option, $value) . "</optgroup>";
                } else {
                    $selected   = ((string) $index === (string) $value) ? ' selected' : '';
                    $list       .= "<option value=\"{$index}\"{$selected}>{$option}</option>";
                }
            }
        } else {
            $inputAttributes = $this->htmlInputAttributes();

            foreach ($options as $index => $option) {
                // typecasted for non-associative arrays
                $selected = ((string) $index === (string) $value) ? " checked=\"yes\"" : '';
                $class = htmlentities("input-single-option-" . str_replace(" ", "-", $index));

                $list .= "<span>" .
                    "<label class=\"{$class}\" title=\"{$option}\">" .
                        "<input type=\"radio\" name=\"{$this->name}\"{$inputAttributes} value=\"{$index}\"{$selected}>" .
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
     **/
    public function __toString(): string
    {
        $marker             = $this->htmlMarker();
        $label              = $this->htmlLabel();
        $list               = $this->htmlList();
        $wrapperAttributes  = $this->htmlWrapperAttributes();
        $errorMessage       = $this->htmlErrorMessage();
        $helpMessage        = $this->htmlHelpMessage();

        if ($this->skin === "select") {
            // render HTML select
            $inputAttributes = $this->htmlInputAttributes();

            return "<p {$wrapperAttributes}>" .
                "<label>" .
                    "<span class=\"depage-label\">{$label}{$marker}</span>" .
                    "<select name=\"{$this->name}\"{$inputAttributes}>{$list}</select>" .
                "</label>" .
                $errorMessage .
                $helpMessage .
            "</p>\n";
        } else {
            // render HTML radio button list
            return "<p {$wrapperAttributes}>" .
                "<span class=\"depage-label\">{$label}{$marker}</span>" .
                "<span>{$list}</span>" .
                $errorMessage .
                $helpMessage .
            "</p>\n";
        }
    }
    // }}}

    // {{{ typeCastValue()
    /**
     * @brief   Converts value to element specific type.
     *
     * @return void
     **/
    protected function typeCastValue(): void
    {
        // check if value is in list
        $inList = false;

        if (in_array($this->value, array_keys($this->list))) {
            $inList = true;
        }
        if (!$inList) {
            foreach ($this->list as $sub) {
                if (is_array($sub) && in_array($this->value, array_keys($sub))) {
                    $inList = true;
                    break;
                }
            }
        }
        if (!$inList) {
            $this->value = "";
        }
        $this->value = (string) $this->value;
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
