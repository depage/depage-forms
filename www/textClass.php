<?php 

require_once ('inputClass.php');

class textClass extends inputClass {
    public function __construct($type, $name, $parameters, $formName) {
        parent::__construct($type, $name, $parameters, $formName);
    }

    public function render() {

        switch ($this->type) {
            case 'text':
            case 'email':
                $renderedInput = '<input name="' . $this->name . '" type="' . $this->type . '">';
                if ($this->label !== '') {
                    $renderedInput = '<label>' . $this->label . $renderedInput . '</label>';
                }
                break;
            case 'hidden':
                $renderedInput = '<input name="' . $this->name . '" type="' . $this->type . '">';
                break;
        }
        return $renderedInput;
    }
}
