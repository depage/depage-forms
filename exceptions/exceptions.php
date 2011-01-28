<?php

class inputNameNoStringException extends inputException {
    public function __construct() {
        parent::__construct("Input name needs to be a string.");
    }
}

class invalidInputNameException extends inputException {
    public function __construct() {
        parent::__construct("Invalid input name.");
    }
}

class unknownInputTypeException extends inputException {
    public function __construct() {
        parent::__construct("Unknown input type.");
    }
}

class inputParametersNoArrayException extends inputException {
    public function __construct() {
        parent::__construct("Input parameters need to be in an array.");
    }
}

class wrongCheckedParameterCombinationException extends inputException {
    public function __construct() {
        parent::__construct("Wrong parameter - option list combination.");
    }
}
