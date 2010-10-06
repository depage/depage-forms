<?php 

require_once ('inputClass.php');

class textClass extends inputClass {
    public function render() {
        $renderedName = ' name="' . $this->name . '"';
        $renderedType = ' type="' . $this->type . '"';
        $renderedValue = (isset($this->value)) ? ' value="' . $this->value . '" ' : '';
        $renderedLabelOpen = ($this->label !== '') ? '<label>' . $this->label : '';
        $renderedLabelClose = ($this->label !== '') ?'</label>' : '';

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
