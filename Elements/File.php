<?php

/**
 * @file    file.php
 * @brief   file input element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace Depage\HtmlForm\Elements;

define("UPLOAD_ERR_FILE_EXTENSION", 1000);

/**
 * @brief   HTML file input type.
 *
 * @todo    dummy - not implemented yet
 **/
class File extends Text
{
    // {{{ variables
    protected $value = [];

    /**
     * @brief HTML maxNum attribute
     **/
    protected $maxNum;

    /**
     * @brief HTML maxSize attribute
     **/
    protected $maxSize;

    /**
     * @brief HTML allowedExtensions attribute
     **/
    protected $allowedExtensions;
    // }}}

    // {{{ setDefaults()
    /**
     * @brief   collects initial values across subclasses
     *
     * The constructor loops through these and creates settable class
     * attributes at runtime. It's a compact mechanism for initialising
     * a lot of variables.
     *
     * @return void
     **/
    protected function setDefaults(): void
    {
        parent::setDefaults();

        // textClass elements have values of type string
        $this->defaults['maxNum'] = 1;
        $this->defaults['maxSize'] = false;
        $this->defaults['allowedExtensions'] = "";
    }
    // }}}

    // {{{ __toString()
    /**
     * @brief   Renders element to HTML.
     *
     * @return string HTML rendered element
     **/
    public function __toString(): string
    {
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
        $helpMessage        = $this->htmlHelpMessage();

        return "<p {$wrapperAttributes}>" .
            "<label>" .
                "<span class=\"depage-label\">{$label}{$marker}</span>" .
                $maxInput .
                "<input name=\"{$this->name}[]\" type=\"{$this->type}\"{$inputAttributes}>" .
                $list .
            "</label>" .
            $errorMessage .
            $helpMessage .
        "</p>\n";
    }
    // }}}
    // {{{ htmlInputAttributes()
    /**
     * @brief renders text element specific HTML attributes
     *
     * @return string $attributes rendered HTML attributes
     **/
    protected function htmlInputAttributes(): string
    {
        $attributes = parent::htmlInputAttributes();

        if ($this->maxNum > 1) {
            $attributes .= " multiple=\"multiple\"";
        }
        if (!empty($this->allowedExtensions)) {
            $attributes .= " accept=\"" . htmlentities($this->allowedExtensions) . "\"";
        }

        return $attributes;
    }
    // }}}

    // {{{ typeCastValue()
    /**
     * @brief   Converts value to element specific type.
     *
     * @return void
     **/
    protected function typeCastValue(): void
    {
        $this->value = (array) $this->value;
    }
    // }}}

    // {{{ handleUploadedFiles()
    /**
     * @brief saves uploaded files
     *
     * @return array $files returns an array of all uploaded files
     **/
    public function handleUploadedFiles(array $files = null): array
    {
        if (!is_array($files)) {
            $files = [];
        }
        $extRegex = "";
        if (!empty($this->allowedExtensions)) {
            $extRegex = str_replace([" ", ",", "."], ["", "|", "\."], $this->allowedExtensions);
        }
        if (isset($_FILES[$this->name])) {
            foreach ($_FILES[$this->name]["error"] as $key => $error) {
                if (!empty($extRegex) && !preg_match("/.*(" . $extRegex . ")$/i", $_FILES[$this->name]["name"][$key])) {
                    $error = UPLOAD_ERR_FILE_EXTENSION;
                }
                if ($error == UPLOAD_ERR_OK) {
                    $uploadName = $_FILES[$this->name]["tmp_name"][$key];
                    $tmpName = tempnam("", "depage-form-upload-");
                    $success = move_uploaded_file($uploadName, $tmpName);
                    if ($this->maxNum > 1) {
                        $files[] = [
                            'name' => $_FILES[$this->name]["name"][$key],
                            'tmp_name' => $tmpName,
                        ];

                    } else {
                        $files[0] = [
                            'name' => $_FILES[$this->name]["name"][$key],
                            'tmp_name' => $tmpName,
                        ];
                    }
                } else {
                    $errorMsgs = [
                        UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the upload_max_filesize directive in php.ini.",
                        UPLOAD_ERR_FORM_SIZE => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.",
                        UPLOAD_ERR_PARTIAL => "The uploaded file was only partially uploaded.",
                        UPLOAD_ERR_NO_FILE => "No file was uploaded.",
                        UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder.",
                        UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk.",
                        UPLOAD_ERR_EXTENSION => "A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop.",
                        UPLOAD_ERR_FILE_EXTENSION => "The uploaded file has an unallowed extension.", // @todo add error message to form
                    ];
                    $this->log("htmlform: " . $errorMsgs[$error]);
                    // TODO can't send array here
                    // $this->log($_FILES[$this->name]);
                }
            }
        }

        // truncate files at max
        $this->value = array_slice($files, - $this->maxNum, $this->maxNum);

        return $this->value;
    }
    // }}}

    // {{{ clearValue()
    /**
     * @brief   resets the value to en empty array and cleans uploaded files
     *
     * @return void
     **/
    public function clearValue(): void
    {
        $this->clearUploadedFiles();

        $this->value = [];
    }
    // }}}

    // {{{ clearUploadedFiles()
    /**
     * @brief cleans uploaded files when session is cleared
     **/
    public function clearUploadedFiles(): void
    {
        if (count($this->value)) {
            foreach ($this->value as $file) {
                if (file_exists($file['tmp_name'])) {
                    unlink($file['tmp_name']);
                }
            }
        }
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
