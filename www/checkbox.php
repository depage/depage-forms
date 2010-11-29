<?php 

require_once ('inputClass.php');

/**
 * HTML checkbox input type.
 **/
class checkbox extends checkboxClass {
    /** 
     * Returns true if $option is selected.
     *
     * @param $option option list key or no parameter for single key
     * @return boolean - true if selected - false if not
     **/
    public function isChecked() {
        if ((func_num_args() === 0) && (count($this->optionList) === 1)) {
            return (!empty($this->value[0]));
        } else if ((func_num_args() === 1) && (count($this->optionList) >= 0)) {
            return (is_array($this->value) && (in_array(func_get_arg(0), $this->value)));
        } else {
            throw new wrongCheckedParameterCombination();
        }
    }
}
