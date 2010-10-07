<?php 

require_once ('text.php');
require_once ('email.php');
require_once ('hidden.php');
require_once ('url.php');


class textClass extends inputClass {
    public function render() {
        $renderedName = ' name="' . $this->name . '"';
        $renderedType = ' type="' . $this->type . '"';
        $renderedValue = (isset($this->value)) ? ' value="' . $this->value . '" ' : '';
        $renderedLabelOpen = (!empty($this->label)) ? '<label>' . $this->label : '';
        $renderedLabelClose = (!empty($this->label)) ?'</label>' : '';
        
        $renderedClass = ' class="';
        foreach($this->getClasses() as $class) {
            $renderedClass .= $class . ' ';
        }
        $renderedClass[strlen($renderedClass)-1] = '"';

        switch ($this->type) {
            case 'text':
            case 'email':
                $renderedInput = $renderedLabelOpen . '<input' . $renderedName . $renderedType . $renderedClass . $renderedValue . '>' . $renderedLabelClose;
                break;
            case 'hidden':
                $renderedInput = '<input' . $renderedName . $renderedType . $renderedClass . $renderedValue . '>';
                break;
        }
        return $renderedInput;
    }
}
