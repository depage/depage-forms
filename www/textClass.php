<?php 

require_once ('text.php');
require_once ('email.php');
require_once ('hidden.php');
require_once ('url.php');


class textClass extends inputClass {
    public function render() {
        $renderedName = ' name="' . $this->name . '"';
        $renderedType = ' type="' . $this->type . '"';
        $renderedValue = (isset($this->value)) ? ' value="' . $this->value . '"' : '';
        $renderedLabelOpen = (!empty($this->label)) ? '<label>' . $this->label : '';
        $renderedLabelClose = (!empty($this->label)) ?'</label>' : '';
        
        switch ($this->type) {
            case 'text':
            case 'email':
                $renderedInput = $renderedLabelOpen . '<input' . $renderedName . $renderedType . $renderedValue . '>' . $renderedLabelClose;
                break;
            case 'hidden':
                $renderedInput = '<input' . $renderedName . $renderedType . $renderedValue . '>';
                break;
        }
        return $renderedInput;
    }
}
