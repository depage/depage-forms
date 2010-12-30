<?php

class containerException extends Exception {
}

class containerNameNoStringException extends containerException {
    public function __construct() {
        parent::__construct("Container name needs to be a string.");
    }
}

class invalidContainerNameException extends containerException {
    public function __construct() {
        parent::__construct("Invalid container name.");
    }
}

class duplicateElementNameException extends containerException {
    public function __construct() {
        parent::__construct("Element name already in use.");
    }
}


class htmlNoStringException extends Exception {
    public function __construct() {
        parent::__construct("HTML needs to be parsed as type string.");
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
