<?php

/**
 * @file    file.php
 * @brief   file input element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace Depage\HtmlForm\Elements;

use Depage\HtmlForm\Abstracts;

/**
 * @brief   HTML file input type.
 *
 * @todo    dummy - not implemented yet
 **/
class File extends Abstracts\Input
{
    public const UPLOAD_ERR_FILE_EXTENSION = 1000;

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
    public function handleUploadedFiles(?array $files = null): array
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
                    $error = self::UPLOAD_ERR_FILE_EXTENSION;
                }
                if ($error == UPLOAD_ERR_OK) {
                    $uploadName = $_FILES[$this->name]["tmp_name"][$key];
                    $safeFilename = $this->sanitizeFilename($_FILES[$this->name]["name"][$key]);

                    // Validate MIME type
                    if (function_exists('finfo_open') && $this->isAllowedMimeType($uploadName, $safeFilename)) {
                        $uploadName = $uploadName; // ok
                    } else {
                        $this->log("htmlform: Uploaded file has disallowed MIME type.");
                        $error = self::UPLOAD_ERR_FILE_EXTENSION;
                    }

                    if ($error == UPLOAD_ERR_OK) {
                        $tmpName = sys_get_temp_dir() . '/htmlforms/' . session_id() . '_' . uniqid('depage-form-upload-', true);
                        $success = move_uploaded_file($uploadName, $tmpName);
                        if (!$success) {
                            $this->log("htmlform: Failed to move uploaded file to secure temp location.");
                            $error = UPLOAD_ERR_CANT_WRITE;
                        }
                    }

                    if ($error == UPLOAD_ERR_OK) {
                        if ($this->maxNum > 1) {
                            $files[] = [
                                'name' => $safeFilename,
                                'tmp_name' => $tmpName,
                            ];

                        } else {
                            $files[0] = [
                                'name' => $safeFilename,
                                'tmp_name' => $tmpName,
                            ];
                        }
                    } else {
                        if (isset($tmpName) && file_exists($tmpName)) {
                            unlink($tmpName);
                        }
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
                        self::UPLOAD_ERR_FILE_EXTENSION => "The uploaded file has an unallowed extension.", // @todo add error message to form
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

    // {{{ sanitizeFilename()
    /**
     * @brief Sanitizes a filename to prevent directory traversal and injection
     *
     * @param string $filename raw filename from upload
     * @return string sanitized filename
     **/
    protected function sanitizeFilename(string $filename): string
    {
        // Use basename to strip any path components
        $filename = basename($filename);

        // Remove null bytes and non-printable characters
        $filename = preg_replace('/[\x00-\x1f\x7f]/', '', $filename);

        // Only allow safe filename characters, then trim to prevent overlong names
        $filename = preg_replace('/[^a-zA-Z0-9\.\-_]/', '_', $filename);
        $filename = substr($filename, 0, 255);

        // Prevent hidden files and dangerous extensions
        $filename = ltrim($filename, '.');

        // Final safety: only allow safe characters
        $filename = preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $filename);

        return !empty($filename) ? $filename : 'uploaded_file';
    }
    // }}}

    // {{{ getAllowedMimeTypes()
    /**
     * @brief Returns the list of allowed MIME types based on allowedExtensions
     *
     * @return array list of allowed MIME type patterns
     **/
    protected function getAllowedMimeTypes(): array
    {
        // Map common extensions to MIME types
        $extensionMap = [
            'jpg'    => ['image/jpeg'],
            'jpeg'   => ['image/jpeg'],
            'png'    => ['image/png'],
            'gif'    => ['image/gif'],
            'bmp'    => ['image/bmp'],
            'webp'   => ['image/webp'],
            'tiff'   => ['image/tiff'],
            'svg'    => ['image/svg+xml'],
            'pdf'    => ['application/pdf'],
            'doc'    => ['application/msword'],
            'docx'   => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'xls'    => ['application/vnd.ms-excel'],
            'xlsx'   => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            'csv'    => ['text/csv'],
            'txt'    => ['text/plain'],
            'rtf'    => ['application/rtf'],
            'odt'    => ['application/vnd.oasis.opendocument.text'],
            'ods'    => ['application/vnd.oasis.opendocument.spreadsheet'],
            'zip'    => ['application/zip'],
            'gz'     => ['application/gzip'],
            'tar'    => ['application/x-tar'],
            'xml'    => ['application/xml', 'text/xml'],
            'json'   => ['application/json'],
            'mp3'    => ['audio/mpeg'],
            'mp4'    => ['video/mp4'],
            'mpeg'   => ['video/mpeg'],
            'ogg'    => ['application/ogg', 'audio/ogg'],
            'ogv'    => ['video/ogg'],
            'webm'   => ['video/webm'],
            'aac'    => ['audio/aac'],
            'wav'    => ['audio/wav'],
            'eps'    => ['application/postscript'],
            'ps'     => ['application/postscript'],
        ];

        $allowedMimes = [];
        if (!empty($this->allowedExtensions)) {
            $extensions = array_map('trim', explode(',', $this->allowedExtensions));
            foreach ($extensions as $ext) {
                $ext = strtolower($ext);
                if (isset($extensionMap[$ext])) {
                    $allowedMimes = array_merge($allowedMimes, $extensionMap[$ext]);
                }
            }
        }

        return $allowedMimes;
    }
    // }}}

    // {{{ isAllowedMimeType()
    /**
     * @brief Checks if a file's MIME type is allowed
     *
     * @param string $uploadPath path to the uploaded temp file
     * @param string $safeFilename sanitized filename for logging
     * @return bool true if MIME type is allowed
     **/
    protected function isAllowedMimeType(string $uploadPath, string $safeFilename): bool
    {
        $allowedMimes = $this->getAllowedMimeTypes();

        // If no extensions are specified, allow all (backwards compatible)
        if (empty($allowedMimes)) {
            return true;
        }

        // Use finfo for reliable MIME detection
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $uploadPath);
            finfo_close($finfo);
        } else {
            // Fallback: attempt basic detection via file extension
            $ext = strtolower(pathinfo($safeFilename, PATHINFO_EXTENSION));
            $extMap = [
                'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
                'gif' => 'image/gif', 'bmp' => 'image/bmp', 'webp' => 'image/webp',
                'pdf' => 'application/pdf', 'zip' => 'application/zip',
            ];
            $mimeType = $extMap[$ext] ?? 'application/octet-stream';
        }

        return in_array($mimeType, $allowedMimes);
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
