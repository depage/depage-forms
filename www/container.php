<?php 

require_once('exceptions.php');
require_once('fieldset.php');
require_once('formClass.php');

/**
 * container
 *
 * The abstract container class contains the similatrities of the child classes
 * formClass and fieldset.
 * 
 **/
abstract class container {
    /**
     * Name of the container.
     **/
    protected $name;
    /**
     * Is true if all container elements are valid, false if one of them isn't.
     **/
    protected $valid;
    /**
     * Holds input element references.
     **/
    protected $inputs = array();
    /**
     * Contains default attribute values.
     **/
    protected $defaults = array();

    /**
     *  @param $name string - container name
     *  @param $parameters array of container parameters, HTML attributes
     *  @return void
     **/
    public function __construct($name, $parameters = array()) {
        $this->checkContainerName($name);
        $this->name = $name;

        // loads default attributes from $this->defaults array
        $this->setDefaults();
        foreach ($this->defaults as $parameter => $default) {
            $this->$parameter = isset($parameters[$parameter]) ? $parameters[$parameter] : $default;
        }
    }

    /**
     * Hook method to be overridden by children in order to specify default
     * values.
     **/
    protected function setDefaults() {
    }

    /**
     * Used to accommodate different input element types. Accepts methods that
     * start with "add" and tries to call $this->addInput() to instantiate
     * the respective class.
     *
     * @param $name string - name of the element
     * @param $parameters array of element attributes: HTML attributes, validation parameters etc.
     **/
    public function __call($functionName, $functionArguments) {
        if (substr($functionName, 0, 3) === 'add') {
            $type = strtolower(str_replace('add', '', $functionName));
            $name = (isset($functionArguments[0])) ? $functionArguments[0] : '';
            $parameters = isset($functionArguments[1]) ? $functionArguments[1] : array();
            return $this->addInput($type, $name, $parameters);
        }
    }

    /**
     * Generates input elements and adds references to them to $this->$inputs.
     * 
     * @param $type input type or fieldset
     * @param $name string - name of the element
     * @param $parameters array of element attributes: HTML attributes, validation parameters etc.
     * @return object $newInput
     **/
    public function addInput($type, $name, $parameters = array()) {
        $this->_checkInputType($type);

        $newInput = new $type($name, $parameters, $this->name);

        $this->inputs[] = $newInput;

        return $newInput;
    }

    /**
     * Walks recursively through current containers' elements and checks if
     * they're valid. Saves the outcome to $this->valid.
     *
     * @return void
     **/
    public function validate() {
        $this->valid = true;
        foreach($this->inputs as $input) {
            $input->validate();
            $this->valid = (($this->valid) && ($input->isValid()));
        }
    }

    /**
     * Returns current containers' validation status.
     *
     * @return $this->valid
     **/
    public function isValid() {
        return $this->valid;
    }

    /**
     * Sets current containers' elements' required-HTML attribute recursively
     * to true.
     *
     * @return void
     **/
    public function setRequired() {
        foreach ($this->inputs as $input) {
            $input->setRequired();
        }
    }

    /**
     * Checks if desired container name is a valid string. Throws an exception
     * if it isn't.
     *
     * @param $name container name to be checked
     **/
    protected function checkContainerName($name) {
        if (!is_string($name)) {
            throw new formNameNoStringException();
        }
        if (trim($name) === '') {
            throw new invalidFormNameException();
        }
    }

    /** 
     * Checks if an input type class exists. Throws an exception if it doesn't.
     *
     * @param $type input type to be checked
     **/
    private function _checkInputType($type) {
        if (!class_exists($type)) {
            throw new unknownInputTypeException();
        }
    }

    /**
     * Returns current containers name.
     *
     * @return $this->name
     **/
    public function getName() {
        return $this->name;
    }

    /**
     * Walks recursively through current containers' elements to compile a list
     * of input elements.
     *
     * @return $allInputs array of input elements
     **/
    public function getInputs() {
        $allInputs = array();
        foreach($this->inputs as $input) {  
            if (is_a($input, 'fieldset')) {
                $allInputs = array_merge($allInputs, $input->getInputs());
            } else {
                $allInputs[] = $input;
            }
        }
        return $allInputs;
    }
}
