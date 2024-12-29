<?php

use PHPUnit\Framework\TestCase;
use Depage\HtmlForm\Abstracts\HtmlDom;

/**
 * Tests for boolean input element rendering.
 **/
class htmlDomTest extends TestCase
{
    protected $htmldom;

    // {{{ setUp()
    public function setUp(): void
    {
        $this->htmldom = new HtmlDom();
    }
    // }}}

    // {{{ testToString()
    /**
     * Test parsing and automatic newslines for output
     **/
    public function testToString()
    {
        $this->htmldom->loadHTML("<p>line 1</p><p>line 2</p>");
        $expected = "<p>line 1</p>\n"
            . "<p>line 2</p>\n";

        $this->assertEquals($expected, $this->htmldom->__toString());
    }
    // }}}
    // {{{ testCleanHtmlTags()
    /**
     * Test cleaning of html tags
     **/
    public function testCleanHtmlTags()
    {
        $this->htmldom->loadHTML("<script>bla</script><h1>headline 1</h1><p>line 2 <b>bold</b></p>");
        $this->htmldom->cleanHtml(['p']);

        $expected = "<p>headline 1</p>\n"
            . "<p>line 2 bold</p>\n";

        $this->assertEquals($expected, $this->htmldom->__toString());
    }
    // }}}
    // {{{ testCleanHtmlClassAttributes()
    /**
     * Test cleaning of html class attributes
     **/
    public function testCleanHtmlClassAttributes()
    {
        $this->htmldom->loadHTML("<p class='allowed not-allowed'>line 1</p><p class='not-allowed'>line 2</p>");
        $this->htmldom->cleanHtml([
            'p.allowed',
        ]);

        $expected = "<p class=\"allowed\">line 1</p>\n"
            . "<p>line 2</p>\n";

        $this->assertEquals($expected, $this->htmldom->__toString());
    }
    // }}}
    // {{{ testCleanHtmlAttributes()
    /**
     * Test cleaning of html attributes
     **/
    public function testCleanHtmlAttributes()
    {
        $this->htmldom->loadHTML("<p onClick='not-allowed'>line 1</p>");
        $this->htmldom->cleanHtml();

        $expected = "<p>line 1</p>\n";

        $this->assertEquals($expected, $this->htmldom->__toString());
    }
    // }}}
    // {{{ testEmptyParagraphs()
    /**
     * Test fixing of empty paragraphs
     **/
    public function testEmptyParagraphs()
    {
        $this->htmldom->loadHTML("<p>line 1</p><p></p><p>line 2</p>");
        $this->htmldom->cleanHtml();

        $expected = "<p>line 1</p>\n"
            . "<p><br></p>\n"
            . "<p>line 2</p>\n";

        $this->assertEquals($expected, $this->htmldom->__toString());
    }
    // }}}
    // {{{ testListFix()
    /**
     * Test fixing of list elements
     **/
    public function testListFix()
    {
        $this->htmldom->loadHTML("<li>line 1</li><li>line 2</li>");
        $this->htmldom->cleanHtml();

        $expected = "<ul>"
            . "<li>line 1</li>"
            . "<li>line 2</li>"
            . "</ul>\n";

        $this->assertEquals($expected, $this->htmldom->__toString());
    }
    // }}}
    // {{{ testRemoveEmptyInlineElements()
    /**
     * Test removing of empty inline elements
     **/
    public function testRemoveEmptyInlineElements()
    {
        $this->htmldom->loadHTML("<p>line 1 <b></b> line 2</p>");
        $this->htmldom->cleanHtml();

        $expected = "<p>line 1  line 2</p>\n";

        $this->assertEquals($expected, $this->htmldom->__toString());
    }
    // }}}
    // {{{ testCutToMaxLength()
    /**
     * Test cutting of text to max length
     **/
    public function testCutToMaxLength()
    {
        $this->htmldom->loadHTML("<p>line 1</p><p>this is a longer text</p>");
        $this->htmldom->cutToMaxLength(8);

        $expected = "<p>line 1</p>\n"
            . "<p>th</p>\n";

        $this->assertEquals($expected, $this->htmldom->__toString());
    }
    // }}}
    // {{{ testCutToMaxLengthWithChild()
    /**
     * Test cutting of text to max length with child element
     **/
    public function testCutToMaxLengthWithChild()
    {
        $this->htmldom->loadHTML("<p>line 1</p><p><b>this</b> is a longer text</p>");
        $this->htmldom->cutToMaxLength(8);

        $expected = "<p>line 1</p>\n"
            . "<p><b>th</b></p>\n";

        $this->assertEquals($expected, $this->htmldom->__toString());
    }
    // }}}
    // {{{ testEmptyContent()
    /**
     * Test empty content
     **/
    public function testEmptyContent()
    {
        $this->htmldom->loadHTML("");
        $this->htmldom->cleanHtml();

        $expected = "";

        $this->assertEquals($expected, $this->htmldom->__toString());
    }
    // }}}
    // {{{ testEmptyContentParagraph()
    /**
     * Test empty content
     **/
    public function testEmptyContentParagraph()
    {
        $this->htmldom->loadHTML("<p></p>");
        $this->htmldom->cleanHtml();

        $expected = "";

        $this->assertEquals($expected, $this->htmldom->__toString());
    }
    // }}}
}
