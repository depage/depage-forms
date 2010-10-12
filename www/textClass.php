<?php 

require_once ('text.php');
require_once ('email.php');
require_once ('hidden.php');
require_once ('url.php');

abstract class textClass extends inputClass {
    public function __construct($name, $parameters, $formName) {
        parent::__construct($name, $parameters, $formName);
        $this->value = (isset($parameters['value'])) ? $parameters['value'] : '';
    }
}
