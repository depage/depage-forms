<?php

class formException extends Exception {
}

class formNameNoStringException extends formException {
    public function __construct() {
        parent::__construct("Form name needs to be a string.");
    }
}

class invalidFormNameException extends formException {
    public function __construct() {
        parent::__construct("Invalid form name.");
    }
}

class inputException extends Exception {
}

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

class duplicateInputNameException extends inputException {
    public function __construct() {
        parent::__construct("Input name already in use.");
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
