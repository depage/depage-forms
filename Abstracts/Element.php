<?php

/**
 * @file    element.php
 * @brief   element class
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace Depage\HtmlForm\Abstracts;

use Depage\HtmlForm\Exceptions;

/**
 * @brief behold: the über-class
 *
 * The abstract element class contains the basic attributes and tools of
 * container and input elements.
 **/
abstract class Element
{
    // {{{ variables
    /**
     * @brief Element name.
     **/
    protected $name;

    /**
     * @brief Contains element validation status/result.
     **/
    public $valid;

    /**
     * @brief True if the element has been validated before.
     **/
    protected $validated = false;

    /**
     * @brief Log object reference
     **/
    protected $log;

    /**
     * @brief holds default values for element attributes
     **/
    protected $defaults = [];

    /**
     * @addtogroup htmlformInputDefaults
     *
     * @default bool $disabled
     *     Enables/disables an input
     **/
    /**
     * @brief wether a input element will be disabled
     **/
    protected $disabled = false;

    /**
     * @addtogroup htmlformInputDefaults
     *
     * @default variant $dataAttr
     *     Optional associated Array of values that will be added as data-Attribute
     *     to the container element
     **/
    /**
     * @brief Extra information about the data that is saved inside the element.
     **/
    public $dataAttr;

    /**
     * @addtogroup htmlformInputDefaults
     *
     * @default variant $class
     *     Optional CSS class of the container element
     **/
    /**
     * @brief CSS class of the container element.
     **/
    protected $class;
    // }}}

    // {{{ __construct()
    /**
     * @brief   element class constructor
     *
     * @param  string $name       element name
     * @param  array  $parameters element parameters, HTML attributes
     * @param  object $form       parent form object reference
     * @return void
     **/
    public function __construct(string $name, array $parameters, object|null $form)
    {
        $this->checkName($name);

        $this->name = $name;

        $this->setDefaults();
        $parameters = array_change_key_case($parameters);
        foreach ($this->defaults as $parameter => $default) {
            $this->$parameter = isset($parameters[strtolower($parameter)]) ? $parameters[strtolower($parameter)] : $default;
        }
    }
    // }}}

    // {{{ setDefaults()
    /**
     * @brief   Collects initial values across subclasses.
     *
     * The constructor loops through these and creates settable class
     * attributes at runtime. It's a compact mechanism for initialising
     * a lot of variables.
     *
     * @return void
     **/
    protected function setDefaults(): void
    {
        $this->defaults['log'] = null;
        $this->defaults['class'] = null;
        $this->defaults['dataAttr'] = [];
        $this->defaults['disabled'] = false;
    }
    // }}}

    // {{{ __call()
    /**
     * @brief   HTML escaping
     *
     * Returns respective HTML escaped attributes for element rendering. Has to
     * be called before printing user-entered data.
     *
     * @param  string $function  function name
     * @param  array  $arguments function arguments
     * @return mixed  HTML escaped value
     *
     * @see     htmlEscape()
     **/
    public function __call(string $function, array $arguments): mixed
    {
        if (substr($function, 0, 4) === 'html') {
            $attribute = str_replace('html', '', $function);
            $attribute[0] = strtolower($attribute[0]);

            $escapedAttribute = $attribute . "Html";

            if (!empty($this->$escapedAttribute)) {
                return $this->$escapedAttribute;
            }
            if (!isset($this->$attribute)) {
                trigger_error("Call to undefined method $function", E_USER_ERROR);
            }

            return $this->htmlEscape($this->$attribute);
        } else {
            trigger_error("Call to undefined method $function", E_USER_ERROR);
        }
    }
    // }}}

    // {{{ setDisabled()
    /**
     * @brief   Sets the HTML disabled-attribute of the current input element.
     *
     * @param  bool $disabled HTML disabled-attribute
     * @return void
     **/
    public function setDisabled(bool $disabled = true): self
    {
        $this->disabled = (bool) $disabled;

        return $this;
    }
    // }}}

    // {{{ getDisabled()
    /**
     * @brief   Gets if input is currently disabled
     *
     * @param  bool $disabled HTML disabled-attribute
     * @return void
     **/
    public function getDisabled(): bool
    {
        return $this->disabled;
    }
    // }}}

    // {{{ clearValue()
    /**
     * @brief   resets the value to null
     *
     * This needs to be refined in the subclasses which really provide an input.
     * It is defined on this abstract class since clearSession calls it for every element in the form
     * even if there is no value.
     *
     * @return void
     **/
    public function clearValue(): void {}
    // }}}

    // {{{ getName()
    /**
     * @brief   Returns the element name.
     *
     * @return string $this->name element name
     **/
    public function getName(): string
    {
        return $this->name;
    }
    // }}}

    // {{{ checkName()
    /**
     * @brief   checks for valid element name
     *
     * Checks that element name is of type string, not empty and doesn't
     * contain invalid characters. Otherwise throws an exception.
     *
     * @param  string $name element name
     * @return void
     **/
    private function checkName($name)
    {
        if (
            !is_string($name)
            || trim($name) === ''
            || preg_match('/[^a-zA-Z0-9_\-\[\]]/', $name)
        ) {
            throw new Exceptions\InvalidElementNameException('"' . $name . '" is not a valid element name.');
        }
    }
    // }}}

    // {{{ log()
    /**
     * @brief error & warning logger
     *
     * If the element is constructed with a custom log object the logging
     * happens there, otherwise the PHP error_log function is used.
     *
     * @param  string $argument message
     * @param  string $type     type of log message
     * @return void
     **/
    protected function log(string $argument, string $type = null): void
    {
        if (is_callable([$this->log, 'log'])) {
            $this->log->log($argument, $type);
        } else {
            if (gettype($argument) != 'string') {
                ob_start();
                print_r($argument);
                $message = ob_get_contents();
                ob_end_clean();
            } else {
                $message = $argument;
            }
            error_log($message);
        }
    }
    // }}}

    // {{{ htmlEscape()
    /**
     * @brief   Escapes HTML in strings and arrays of strings
     *
     * @param  mixed $options value to be HTML-escaped
     * @return mixed $htmlOptions    HTML escaped value
     *
     * @see     __call()
     **/
    protected function htmlEscape(array|string $options = []): array|string
    {
        if (is_string($options)) {
            $htmlOptions = htmlspecialchars($options, ENT_QUOTES);
        } elseif (is_array($options)) {
            $htmlOptions = [];

            foreach ($options as $index => $option) {
                if (is_string($index)) {
                    $index  = htmlspecialchars($index, ENT_QUOTES);
                }
                if (is_string($option)) {
                    $option = htmlspecialchars($option, ENT_QUOTES);
                }

                $htmlOptions[$index] = $option;
            }
        } else {
            $htmlOptions = $options;
        }

        return $htmlOptions;
    }
    // }}}

    // {{{ htmlDataAttributes()
    /**
     * @brief   Returns dataAttr escaped as attribute string
     **/
    protected function htmlDataAttributes(): string
    {
        $attributes = "";
        if (is_array($this->dataAttr)) {
            foreach ($this->dataAttr as $key => $val) {
                // @todo throw error when key is not plain string?
                $attributes .= " data-$key=\"" . $this->htmlEscape($val) . "\"";
            }
        }

        return $attributes;
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
