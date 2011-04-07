<?php
namespace depage\htmlform\validators;

class url extends validator {
    public function validate($url, $parameters = array()) {
        return (bool) filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED + FILTER_FLAG_HOST_REQUIRED);
    }
}
