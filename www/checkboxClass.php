<?php 

require_once ('inputClass.php');

class checkboxClass extends inputClass {
    private $optionList = array();
    
    public function __construct($type, $name, $parameters, $formName) {
        parent::__construct($type, $name, $parameters, $formName);
        $this->optionList = $parameters['optionList'];
    }

    public function render() {
        switch ($this->type) {
            case 'checkbox':
                $renderedInput = '<input name="' . $this->name . '" type="' . $this->type . '">';
                break;
            case 'select':
                foreach($this->optionList as $option) {
                    $renderedInput .= '<option>' . $option . '</option>';
                }
                $renderedInput = '<select name="' . $this->name . '">' . $renderedInput . '</select>';
                break;
        }

        if ($this->label !== '') {
            $renderedInput = '<label>' . $this->label . $renderedInput . '</label>';
        }

        return $renderedInput;
    }
}
