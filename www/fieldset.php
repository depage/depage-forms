<?php 

require_once('container.php');

class fieldset extends container {
    private $form;

    public function __construct($name, $parameters = array()) {
        parent::__construct($name, $parameters);

        $this->form = $parameters['form'];
    }

    protected function setDefaults() {
        parent::setDefauls();
        $this->defaults['label'] = $this->name;
    }

    public function addInput($type, $name, $parameters = array()) {
        $this->form->checkInputName($name);

        if ($type === 'fieldset') {
            $parameters['form'] = $this->form;
        }

        $newInput = parent::addInput($type, $name, $parameters);
        
        $this->form->loadValueFromSession($name);

        return $newInput;
    }

    public function __toString() {
        $renderedInputs = '';
        foreach($this->inputs as $input) {
            $renderedInputs .= $input;
        }
        return "<fieldset id=\"$this->name\" name=\"$this->name\"><legend>$this->label</legend>$renderedInputs</fieldset>";
    }

    public function getInputs() {
        return $this->inputs;
    }

    public function getInput($name) { // @todo useless?
        foreach($this->inputs as $index => $input) {
            if ($name === $input->getName()) {
                return $input;
            }
        }
    }

    public function populate($data = array()) {
        foreach($data as $name => $value) {
            $this->getInput($name)->setValue($value);
        }
    }
}
