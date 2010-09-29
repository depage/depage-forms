<?php 

require_once('inputClass.php');
require_once('textClass.php');

class formClass {
    private $submitButtonLabel = 'go';
    public $inputs = array();

    public function __construct($submitButtonLabel) {
        $this->submitButtonLabel = $submitButtonLabel;
    }

    public function addHidden($name, $attributes = array()) {
        $this->addInput('hidden', $name, $attributes);
    }

    public function addText($name, $attributes = array()) {
        $this->addInput('text', $name, $attributes);
    }
    
    public function addInput($type, $name, $attributes = array()) {
        switch ($type) {
            case 'hidden':
            case 'text':
                $class = 'textClass';
                break;
            default:
        }        
        $this->inputs[] = new $class($type, $name, $attributes);
    }
}
