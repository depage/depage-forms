<?php
/**
 * @file    richtext.php
 * @brief   richtext input element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace depage\htmlform\elements;

/**
 * @brief HTML richtext element.
 *
 * adds a textarea-element with additional richtext-functionality 
 * added by javascript
 *
 * @section usage
 *
 * @code
 * <?php
 *     $form = new depage\htmlform\htmlform('myform');
 *
 *     // add a richtext field
 *     $form->addRichtext('html', array(
 *         'label' => 'Richtext box',
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
class richtext extends textarea {
    // {{{ setDefaults()
    /**
     * @brief   collects initial values across subclasses
     *
     * @return  void
     **/
    protected function setDefaults() {
        parent::setDefaults();

        $this->defaults['rows'] = null;
        $this->defaults['cols'] = null;
        $this->defaults['stylesheet'] = null;
        $this->defaults['autogrow'] = true;
    }
    // }}}
    // {{{ htmlWrapperAttributes()
    /**
     * @brief   Returns string of HTML attributes for element wrapper paragraph.
     *
     * @return  $attributes (string) HTML attribute
     **/
    protected function htmlWrapperAttributes() {
        $attributes = parent::htmlWrapperAttributes();

        $options = array();
        $options['autogrow'] = $this->autogrow;
        $options['stylesheet'] = $this->stylesheet;

        $attributes .= " data-richtext-options=\"" . $this->htmlEscape(json_encode($options)) . "\"";

        return $attributes;
    }
    // }}}
}

/* vim:set ft=php fenc=UTF-8 sw=4 sts=4 fdm=marker et : */
