<?php 

require_once ('inputClass.php');

class textClass extends inputClass {
    public function render() {
        $renderedName = ' name="' . $this->name . '"';
        $renderedType = ' type="' . $this->type . '"';
        $renderedValue = (isset($this->value)) ? ' value="' . $this->value . '" ' : '';
        $renderedLabelOpen = ($this->label !== '') ? '<label>' . $this->label : '';
        $renderedLabelClose = ($this->label !== '') ?'</label>' : '';
        
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
