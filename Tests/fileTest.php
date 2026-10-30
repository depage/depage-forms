<?php

namespace Depage\HtmlForm\Elements {
    // {{{ move_uploaded_file()
    /**
     * Namespaced stub that shadows the global move_uploaded_file() for the
     * File element. PHP resolves unqualified function calls in the class
     * namespace first, so File::handleUploadedFiles() ends up here. This
     * lets us test the upload handling without a real HTTP upload.
     **/
    function move_uploaded_file(string $from, string $to): bool
    {
        return \fileTestUploadStub::move($from, $to);
    }
    // }}}
}

namespace {
    use PHPUnit\Framework\TestCase;
    use Depage\HtmlForm\Elements\File;

    // {{{ fileTestUploadStub
    /**
     * Collects the move operations of the move_uploaded_file() stub above
     * so the tests can inspect and clean up after them.
     **/
    class fileTestUploadStub
    {
        public static $moved = [];
        public static $fail = false;

        public static function move(string $from, string $to): bool
        {
            if (self::$fail || !file_exists($from)) {
                return false;
            }

            $success = copy($from, $to);
            if ($success) {
                unlink($from);
                self::$moved[] = $to;
            }

            return $success;
        }

        public static function reset(): void
        {
            self::$moved = [];
            self::$fail = false;
        }
    }
    // }}}

    // {{{ fileTest
    /**
     * Tests the file input element, mainly the handling of uploaded files.
     **/
    class fileTest extends TestCase
    {
        protected $form;
        protected $log;
        protected $element;
        protected $sourceFiles = [];

        // {{{ setUp()
        public function setUp(): void
        {
            fileTestUploadStub::reset();

            $this->form     = new nameTestForm();
            $this->log      = new logTestClass();
            $this->element  = new File('fileString', ['log' => $this->log], $this->form);

            unset($_FILES['fileString']);
        }
        // }}}
        // {{{ tearDown()
        public function tearDown(): void
        {
            unset($_FILES['fileString']);

            // remove faked upload sources
            foreach ($this->sourceFiles as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }
            $this->sourceFiles = [];

            // remove moved files from the htmlforms temp folder again
            foreach (glob($this->getTmpDir() . '/' . session_id() . '_depage-form-upload-*') ?: [] as $file) {
                unlink($file);
            }
            if (is_dir($this->getTmpDir())) {
                @rmdir($this->getTmpDir());
            }

            fileTestUploadStub::reset();
        }
        // }}}

        // {{{ getTmpDir()
        protected function getTmpDir(): string
        {
            return sys_get_temp_dir() . '/htmlforms';
        }
        // }}}
        // {{{ addFakeUpload()
        /**
         * Adds a file to the $_FILES superglobal as if it had been uploaded
         * through the "fileString[]" input of the browser.
         **/
        protected function addFakeUpload(string $name, string $content = "test file content", int $error = UPLOAD_ERR_OK): string
        {
            $source = tempnam(sys_get_temp_dir(), 'depage-form-upload-test-');
            file_put_contents($source, $content);
            $this->sourceFiles[] = $source;

            $_FILES['fileString']['name'][]       = $name;
            $_FILES['fileString']['type'][]       = 'application/octet-stream';
            $_FILES['fileString']['tmp_name'][]   = $source;
            $_FILES['fileString']['error'][]      = $error;
            $_FILES['fileString']['size'][]       = strlen($content);

            return $source;
        }
        // }}}

        // {{{ testHandleUploadedFilesCreatesTmpFolder()
        /**
         * The htmlforms folder in the system temp directory may not exist
         * yet. handleUploadedFiles() has to create it before moving the
         * uploaded file there.
         **/
        public function testHandleUploadedFilesCreatesTmpFolder()
        {
            $this->assertFalse(is_dir($this->getTmpDir()), "temp folder already exists, cannot test creation");

            $this->addFakeUpload('uploaded.txt');

            $files = $this->element->handleUploadedFiles();

            $this->assertDirectoryExists($this->getTmpDir());
            $this->assertCount(1, $files);
            $this->assertEquals('uploaded.txt', $files[0]['name']);
            $this->assertFileExists($files[0]['tmp_name']);
            $this->assertStringStartsWith($this->getTmpDir() . '/' . session_id() . '_depage-form-upload-', $files[0]['tmp_name']);
            $this->assertEquals("test file content", file_get_contents($files[0]['tmp_name']));
        }
        // }}}
        // {{{ testHandleUploadedFilesWithExistingTmpFolder()
        /**
         * When the htmlforms folder already exists (e.g. second upload in
         * the same session), moving the file must still work.
         **/
        public function testHandleUploadedFilesWithExistingTmpFolder()
        {
            mkdir($this->getTmpDir(), 0700, true);

            $this->addFakeUpload('first.txt');
            $files = $this->element->handleUploadedFiles();

            $this->assertCount(1, $files);
            $this->assertFileExists($files[0]['tmp_name']);

            $this->addFakeUpload('second.txt');
            $files = $this->element->handleUploadedFiles($files);

            $this->assertCount(1, $files);
            $this->assertEquals('second.txt', $files[0]['name']);
            $this->assertFileExists($files[0]['tmp_name']);
        }
        // }}}
        // {{{ testHandleUploadedFilesMaxNum()
        /**
         * With maxNum > 1 all files are kept, but at most maxNum (the
         * newest ones).
         **/
        public function testHandleUploadedFilesMaxNum()
        {
            $this->element = new File('fileString', ['log' => $this->log, 'maxNum' => 2], $this->form);

            $this->addFakeUpload('first.txt');
            $this->addFakeUpload('second.txt');
            $this->addFakeUpload('third.txt');

            $files = $this->element->handleUploadedFiles();

            $this->assertCount(2, $files);
            $this->assertEquals('second.txt', $files[0]['name']);
            $this->assertEquals('third.txt', $files[1]['name']);
        }
        // }}}
        // {{{ testHandleUploadedFilesSanitizesFilename()
        /**
         * Filenames from uploads are user input and have to be sanitized.
         **/
        public function testHandleUploadedFilesSanitizesFilename()
        {
            $this->addFakeUpload('../../evil file?.txt');

            $files = $this->element->handleUploadedFiles();

            $this->assertCount(1, $files);
            $this->assertEquals('evil_file_.txt', $files[0]['name']);
        }
        // }}}
        // {{{ testHandleUploadedFilesDisallowedExtension()
        /**
         * Files with an extension that is not in allowedExtensions must be
         * rejected before they get moved to the temp folder.
         **/
        public function testHandleUploadedFilesDisallowedExtension()
        {
            $this->element = new File('fileString', ['log' => $this->log, 'allowedExtensions' => 'jpg,png'], $this->form);

            $source = $this->addFakeUpload('evil.php', '<?php echo "evil";');

            $files = $this->element->handleUploadedFiles();

            $this->assertCount(0, $files);
            $this->assertCount(0, fileTestUploadStub::$moved);
            $this->assertFileExists($source);
            $this->assertEquals('htmlform: The uploaded file has an unallowed extension.', $this->log->error['argument']);
        }
        // }}}
        // {{{ testHandleUploadedFilesAllowedExtensionAndMimeType()
        /**
         * Files with an allowed extension and a matching MIME type are
         * accepted.
         **/
        public function testHandleUploadedFilesAllowedExtensionAndMimeType()
        {
            if (!function_exists('finfo_open')) {
                $this->markTestSkipped('finfo extension not available');
            }

            $this->element = new File('fileString', ['log' => $this->log, 'allowedExtensions' => 'jpg'], $this->form);

            // minimal JPEG header so finfo detects image/jpeg
            $this->addFakeUpload('photo.jpg', "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x00\x00\x01\x00\x01\x00\x00\xFF\xD9");

            $files = $this->element->handleUploadedFiles();

            $this->assertCount(1, $files);
            $this->assertEquals('photo.jpg', $files[0]['name']);
            $this->assertFileExists($files[0]['tmp_name']);
        }
        // }}}
        // {{{ testHandleUploadedFilesDisallowedMimeType()
        /**
         * Files with an allowed extension but a different MIME type must be
         * rejected.
         **/
        public function testHandleUploadedFilesDisallowedMimeType()
        {
            if (!function_exists('finfo_open')) {
                $this->markTestSkipped('finfo extension not available');
            }

            $this->element = new File('fileString', ['log' => $this->log, 'allowedExtensions' => 'jpg'], $this->form);

            $this->addFakeUpload('fake.jpg', '<?php echo "not a jpeg";');

            $files = $this->element->handleUploadedFiles();

            $this->assertCount(0, $files);
            $this->assertCount(0, fileTestUploadStub::$moved);
            $this->assertEquals('htmlform: Uploaded file has disallowed MIME type.', $this->log->error['argument']);
        }
        // }}}
        // {{{ testHandleUploadedFilesMoveFails()
        /**
         * When moving the file fails, the element must end up empty and
         * log an error.
         **/
        public function testHandleUploadedFilesMoveFails()
        {
            fileTestUploadStub::$fail = true;

            $this->addFakeUpload('uploaded.txt');

            $files = $this->element->handleUploadedFiles();

            $this->assertCount(0, $files);
            $this->assertEquals('htmlform: Failed to move uploaded file to secure temp location.', $this->log->error['argument']);
        }
        // }}}
        // {{{ testHandleUploadedFilesWithoutFiles()
        /**
         * Without any upload data the value stays an empty array.
         **/
        public function testHandleUploadedFilesWithoutFiles()
        {
            $files = $this->element->handleUploadedFiles();

            $this->assertEquals([], $files);
        }
        // }}}
        // {{{ testClearValueRemovesUploadedFiles()
        /**
         * clearValue() removes the moved files from the temp folder.
         **/
        public function testClearValueRemovesUploadedFiles()
        {
            $this->addFakeUpload('uploaded.txt');

            $files = $this->element->handleUploadedFiles();
            $tmpName = $files[0]['tmp_name'];
            $this->assertFileExists($tmpName);

            $this->element->clearValue();

            $this->assertFileDoesNotExist($tmpName);
        }
        // }}}
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
