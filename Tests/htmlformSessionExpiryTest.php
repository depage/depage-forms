<?php

namespace Depage\HtmlForm;

/**
 * overriding time() function in our namespace
 **/
function time()
{
    return 42;
}

class htmlformSessionExpiryTest extends \PHPUnit\Framework\TestCase
{
    // {{{ testSetTimestamp()
    /**
     * see if the timestamp is written to session
     **/
    public function testSetTimestamp()
    {
        $form = new HtmlForm('formName', ['ttl' => 60]);
        $this->assertEquals(42, $_SESSION['htmlform-formName-data']['formTimestamp']);
    }
    // }}}

    // {{{ testUpdateTimestamp()
    /**
     * In an active session only the timestamp should be updated. The form data
     * remains untouched.
     **/
    public function testUpdateTimestamp()
    {
        $_SESSION['htmlform-formName-data']['test'] = 'test';
        $_SESSION['htmlform-formName-data']['formTimestamp'] = 1;
        $form = new HtmlForm('formName', ['ttl' => 60]);

        $this->assertEquals('test', $_SESSION['htmlform-formName-data']['test']);
        $this->assertEquals(42, $_SESSION['htmlform-formName-data']['formTimestamp']);
    }
    // }}}

    // {{{ testClearSession()
    /**
     * If the session timestamp is outdated, form data should be cleared.
     **/
    public function testClearSession()
    {
        $_SESSION['htmlform-formName-data']['test'] = 'test';
        $_SESSION['htmlform-formName-data']['formTimestamp'] = 1;
        $form = new HtmlForm('formName', ['ttl' => 30]);

        $this->assertFalse(isset($_SESSION['htmlform-formName-data']['test']));
        $this->assertEquals(42, $_SESSION['htmlform-formName-data']['formTimestamp']);
    }
    // }}}
}
