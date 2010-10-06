<?php 

require_once ('inputClass.php');

class checkboxClass extends inputClass {
    private $optionList = array();
    
    public function __construct($type, $name, $parameters, $formName) {
        parent::__construct($type, $name, $parameters, $formName);
        $this->optionList = (isset($parameters['optionList'])) ? $parameters['optionList'] : '';
    }

    public function render() {
        $renderedName = ' name="' . $this->name . '"';
        $renderedType = ' type="' . $this->type . '"';

        $renderedLabelOpen = ($this->label !== '') ? '<label>' . $this->label : '';
        $renderedLabelClose = ($this->label !== '') ?'</label>' : '';

        $renderedInput = '';
        switch ($this->type) {
            case 'checkbox':
                $renderedInput = '<input' . $renderedName . $renderedType . '">';
                break;
            case 'select':
                foreach($this->optionList as $option) {
                    $renderedInput .= '<option>' . $option . '</option>';
                }
                $renderedInput = '<select' . $renderedName . '>' . $renderedInput . '</select>';
                break;
        }

        $renderedInput = $renderedLabelOpen . $renderedInput . $renderedLabelClose;

        return $renderedInput;
    }

}
