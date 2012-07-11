<?php
/**
 * @file    file.php
 * @brief   file input element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace depage\htmlform\elements;

/**
 * @brief   HTML file input type.
 *
 * @todo    dummy - not implemented yet
 **/
class file extends text {
    protected $value = array();
    
    // {{{ setDefaults()
    /**
     * @brief   collects initial values across subclasses
     *
     * The constructor loops through these and creates settable class
     * attributes at runtime. It's a compact mechanism for initialising
     * a lot of variables.
     *
     * @return  void
     **/
    protected function setDefaults() {
        parent::setDefaults();

        // textClass elements have values of type string
        $this->defaults['maxNum'] = 1;
        $this->defaults['maxSize'] = false;
    }
    // }}}
    
    // {{{ __toString()
    /**
     * @brief   Renders element to HTML.
     *
     * @return  (string) HTML rendered element
     **/
    public function __toString() {
        if ($this->maxSize !== false) {
            $maxInput = "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"{$this->maxSize}\" />";
        } else {
            $maxInput = "";
        }

        $value              = $this->htmlValue();
        $inputAttributes    = $this->htmlInputAttributes();
        $marker             = $this->htmlMarker();
        $label              = $this->htmlLabel();
        $list               = $this->htmlList();
        $wrapperAttributes  = $this->htmlWrapperAttributes();
        $errorMessage       = $this->htmlErrorMessage();

        return "<p {$wrapperAttributes}>" .
            "<label>" .
                "<span class=\"label\">{$label}{$marker}</span>" .
                $maxInput . 
                "<input name=\"{$this->name}[]\" type=\"{$this->type}\"{$inputAttributes}>" .
                $list .
            "</label>" .
            $errorMessage .
        "</p>\n";
    }
    // }}}
    // {{{ htmlInputAttributes()
    /**
     * @brief renders text element specific HTML attributes
     *
     * @return $attributes (string) rendered HTML attributes
     **/
    protected function htmlInputAttributes() {
        $attributes = parent::htmlInputAttributes();

        if ($this->maxNum > 1) {
            $attributes .= " multiple=\"multiple\"";
        }

        return $attributes;
    }
    // }}}
    
    // {{{ typeCastValue()
    /**
     * @brief   Converts value to element specific type.
     *
     * @return  void
     **/
    protected function typeCastValue() {
        $this->value = (array) $this->value;
    }
    // }}}
    
    // {{{ handleUploadedFiles()
    /**
     * @brief saves uploaded files
     *
     * @return $files (array) returns an array of all uploaded files
     **/
    public function handleUploadedFiles($files = null) {
        if (!is_array($files)) {
            $files = array();
        }
        if (isset($_FILES[$this->name])){
            foreach ($_FILES[$this->name]["error"] as $key => $error) {
                if ($error == UPLOAD_ERR_OK) {
                    $upload_name = $_FILES[$this->name]["tmp_name"][$key];
                    $tmp_name = tempnam(null, "depage-form-upload-");
                    if (move_uploaded_file($upload_name, $tmp_name)) {
                        if ($this->maxNum > 1) {
                            $files[] = array(
                                'name' => $_FILES[$this->name]["name"][$key],
                                'tmp_name' => $tmp_name,
                            );
                            
                        } else {
                            $files[0] = array(
                                'name' => $_FILES[$this->name]["name"][$key],
                                'tmp_name' => $tmp_name,
                            );
                        }
                    } else {
                        $errorMsgs = array(
                            UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the upload_max_filesize directive in php.ini.",
                            UPLOAD_ERR_FORM_SIZE => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.",
                            UPLOAD_ERR_PARTIAL => "The uploaded file was only partially uploaded.",
                            UPLOAD_ERR_NO_FILE => "No file was uploaded.",
                            UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder.",
                            UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk.",
                            UPLOAD_ERR_EXTENSION => "A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop.",
                        );
                        $this->log("htmlform: " . $errorMsgs[$error]);
                        $this->log($_FILES[$this->name]);
                    }
                }
            }
        }
        
        // add new files to beginning
        foreach($files as &$file) {
            array_unshift($this->value, $file);
        }
        // truncate files at max
        $this->value = array_slice($this->value, 0, $this->maxNum); 
        
        return $this->value;
    }
    // }}}
    // {{{ cleanUploadedFiles()
    /**
     * @brief cleans uploaded files when session is cleared
     **/
    public function clearUploadedFiles() {
        if(count($this->value)){
            foreach ($this->value as $file) {
                unlink($file['tmp_name']);
            }
        }
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
