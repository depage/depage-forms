/**
 * @file    src/tests/htmlform-tiptap-richtext.test.js
 * @brief   smoke test for the tiptap richtext backend
 *
 *     bun test src/tests
 **/
import { test, expect, beforeAll, afterAll } from "bun:test";
import { renderFixture } from "./dom.js";

document.body.innerHTML = renderFixture("render-richtext-fixture.php");

const { default: DepageForm } = await import("../htmlform.js");

var previousEditor = DepageForm.defaultEditor;
var container;
var editor;

beforeAll(async () => {
    DepageForm.defaultEditor = "tiptap";

    var form = document.querySelector("form.depage-form");

    container = form.querySelector(".input-richtext");

    var instance = DepageForm.setupForm(form);
    await instance.ready;

    editor = container.querySelector(".textarea.richtext").editor;
});

afterAll(() => {
    DepageForm.defaultEditor = previousEditor;
});

// {{{ setup
test("richtext element is replaced by a tiptap editor", () => {
    expect(container.querySelector("textarea")).toBeNull();
    expect(editor).toBeDefined();

    var wrapper = container.querySelector(".textarea.richtext");
    expect(wrapper.toolbar).toBeDefined();

    // the editable element is the contenteditable element prosemirror
    // creates inside the textarea-content element
    var editable = editor.getEditableElement();
    expect(editable.classList.contains("ProseMirror")).toBe(true);
    expect(editable.parentElement.classList.contains("textarea-content")).toBe(true);
});
// }}}
// {{{ block styles
test("the style of the current block is reported", () => {
    expect(editor.getBlockStyle()).toBe("p");
});
// }}}
// {{{ replaceBlock
test("replaceBlock() sets the tag and the class of the block", () => {
    editor.replaceBlock("h2.teaser");
    expect(editor.getBlockStyle()).toBe("h2.teaser");
    expect(editor.getHTML()).toContain("<h2 ");
    expect(editor.getHTML()).toContain("class=\"teaser\"");

    editor.replaceBlock("p.action");
    expect(editor.getBlockStyle()).toBe("p.action");
    expect(editor.getHTML()).toContain("<p ");
    expect(editor.getHTML()).toContain("class=\"action\"");
});
// }}}
// {{{ replaceBlock without a class
test("replaceBlock() removes the class of the block", () => {
    editor.replaceBlock("p.action");
    expect(editor.getBlockStyle()).toBe("p.action");

    // a style without a class has to reset the class of the block —
    // prosemirror's setNode() copies the attributes of the current block
    editor.replaceBlock("p");
    expect(editor.getBlockStyle()).toBe("p");
    expect(editor.getHTML()).not.toContain("action");
});
// }}}
// {{{ focus
test("focus() accepts a position", () => {
    editor.replaceBlock("p");
    editor.focus("end");

    expect(editor.getBlockStyle()).toBe("p");
});
// }}}

/* vim:set ft=javascript sw=4 sts=4 fdm=marker et : */
