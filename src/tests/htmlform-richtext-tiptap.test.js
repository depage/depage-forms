/**
 * @file    src/tests/htmlform-richtext-tiptap.test.js
 * @brief   smoke test for the tiptap richtext backend
 *
 *     bun test src/tests
 **/
import { test, expect } from "bun:test";
import { renderFixture } from "./dom.js";

document.body.innerHTML = renderFixture("render-richtext-fixture.php");

// a second form whose content comes from depage-xmldb — rendered with
// the addDbids option, so the data-dbid attributes are part of the
// fixture itself
document.body.insertAdjacentHTML("beforeend", renderFixture("render-richtext-dbid-fixture.php"));

const { default: DepageForm } = await import("../htmlform.js");

// {{{ helpers
/**
 * @brief   removes the data-dbid attributes from a html string
 *
 * The editor hands out placeholder ids for the nodes it creates, so the
 * markup can only be compared without them.
 **/
function withoutDbids(html) {
    return html.replace(/ data-dbid="-?\d+"/g, "");
}
// }}}

// {{{ richtext setup with the tiptap backend
test("richtext element is replaced by a tiptap editor", async () => {
    var form = document.querySelector("form.depage-form");
    var container = form.querySelector(".input-richtext");
    var textarea = container.querySelector("textarea");

    expect(textarea).not.toBeNull();

    // select the tiptap backend for this element — the data-dbid
    // attributes are only kept when addDbids is enabled (the fixture
    // content does not come from depage-xmldb)
    var options = JSON.parse(container.getAttribute("data-richtext-options") || "{}");
    options.editor = "tiptap";
    options.addDbids = true;
    container.setAttribute("data-richtext-options", JSON.stringify(options));

    var initialized = null;
    form.addEventListener("depageForm.richtextInitialized", (e) => {
        initialized = e.detail;
    });

    var instance = DepageForm.setupForm(form);
    await instance.ready;

    // textarea is replaced by the richtext div + hidden input
    expect(container.querySelector("textarea")).toBeNull();
    expect(container.querySelector(".textarea.richtext .textarea-content")).not.toBeNull();

    var input = container.querySelector("input[type='hidden']");
    expect(input).not.toBeNull();
    expect(input.name).toBe(textarea.name);
    expect(input.value).toContain("test");

    // the editor facade and toolbar are attached
    var wrapper = container.querySelector(".textarea.richtext");
    expect(wrapper.editor).toBeDefined();
    expect(wrapper.toolbar).toBeDefined();

    // the tiptap editor renders into the textarea-content div; the
    // editable element itself is created as its child by prosemirror
    var div = container.querySelector(".textarea-content");
    var viewDom = wrapper.editor.editor.view.dom;

    expect(wrapper.editor.editor.isEditable).toBe(true);
    expect(div.contains(viewDom)).toBe(true);
    expect(viewDom.getAttribute("contenteditable")).toBe("true");
    expect(div.innerHTML).toContain("test");

    // toolbar buttons for the allowed tags were created
    var toolbar = wrapper.toolbar.toolbar;
    expect(toolbar.querySelectorAll("li").length).toBeGreaterThan(0);
    expect(toolbar.querySelector(".format-b")).not.toBeNull();
    expect(toolbar.querySelector(".format-h1")).not.toBeNull();

    // the facade roundtrips html
    expect(wrapper.editor.getHTML()).toContain("test");

    // the initialized event was dispatched with the facade
    expect(initialized).not.toBeNull();
    expect(initialized.container).toBe(container);
    expect(initialized.editor).toBe(wrapper.editor);
});
// }}}
// {{{ setEditable
test("tiptap editor can be disabled through setEditable", async () => {
    var container = document.querySelector(".input-richtext");
    var wrapper = container.querySelector(".textarea.richtext");

    wrapper.editor.setEditable(false);
    expect(wrapper.editor.editor.isEditable).toBe(false);

    wrapper.editor.setEditable(true);
    expect(wrapper.editor.editor.isEditable).toBe(true);
});
// }}}
// {{{ schema roundtrip
test("tiptap schema keeps depage links, classes and attributes", async () => {
    var container = document.querySelector(".input-richtext");
    var wrapper = container.querySelector(".textarea.richtext");
    var editor = wrapper.editor;

    editor.setHTML('<h1 class="teaser" data-dbid="1">Headline</h1><p data-dbid="2">text <a href="pageref://123" data-dbid="456" class="internal">ref</a></p>');

    var html = editor.getHTML();

    expect(html).toContain("<h1");
    expect(html).toContain("class=\"teaser\"");
    expect(html).toContain("data-dbid=\"1\"");
    expect(html).toContain("data-dbid=\"2\"");
    expect(html).toContain("href=\"pageref://123\"");
    expect(html).toContain("data-dbid=\"456\"");
    expect(html).toContain("class=\"internal\"");

    // unknown protocols are dropped by the link schema
    editor.setHTML('<p><a href="javascript:alert(1)">xss</a></p>');

    html = editor.getHTML();

    expect(html).not.toContain("javascript:");
});
// }}}
// {{{ data-dbid without addDbids
test("tiptap editor drops data-dbid attributes without addDbids", async () => {
    // the main fixture does not enable addDbids — the ids of
    // depage-xmldb are not kept then
    document.body.insertAdjacentHTML("beforeend",
        '<form class="depage-form" id="noDbidForm">' +
        '<p id="noDbidForm-rt" class="input-richtext" ' +
        'data-textarea-options="{}" ' +
        'data-richtext-options=\'{"allowedTags":["p","br","b","i","a"],"editor":"tiptap"}\'>' +
        '<label>No dbids</label>' +
        '<textarea name="rt"></textarea>' +
        '</p></form>'
    );

    var form = document.getElementById("noDbidForm");
    var container = form.querySelector(".input-richtext");

    container.querySelector("textarea").value = '<p data-dbid="7">hello <b data-dbid="8">world</b></p>';

    var instance = DepageForm.setupForm(form);
    await instance.ready;

    var wrapper = container.querySelector(".textarea.richtext");
    var editor = wrapper.editor;

    expect(editor.getHTML()).toBe("<p>hello <strong>world</strong></p>");

    // new blocks get no placeholder id either
    editor.editor.commands.setTextSelection(12);
    editor.editor.commands.keyboardShortcut("Enter");
    editor.editor.commands.insertContent("new text");

    expect(editor.getHTML()).not.toContain("data-dbid");
    expect(container.querySelector("input[type='hidden']").value).not.toContain("data-dbid");

    // setHTML roundtrips without the attribute, too
    editor.setHTML('<h1 data-dbid="1">Headline</h1><p data-dbid="2">text <a href="pageref://123" data-dbid="456">ref</a></p>');

    expect(editor.getHTML()).not.toContain("data-dbid");
    expect(editor.getHTML()).toContain("href=\"pageref://123\"");
});
// }}}
// {{{ data-dbid with addDbids
test("tiptap editor keeps data-dbid attributes with addDbids", async () => {
    // the dbid fixture was rendered with the addDbids option, so the
    // data-dbid attribute of the default value is part of the content
    // the editor reads from the textarea
    var form = document.getElementById("dbidForm");
    var container = form.querySelector(".input-richtext");

    expect(JSON.parse(container.getAttribute("data-richtext-options")).addDbids).toBe(true);

    // select the tiptap backend for this element — the dbid attributes
    // are only part of its schema (squire sanitizes them away unless
    // addDbids is enabled as well)
    var options = JSON.parse(container.getAttribute("data-richtext-options"));
    options.editor = "tiptap";
    container.setAttribute("data-richtext-options", JSON.stringify(options));

    var instance = DepageForm.setupForm(form);
    await instance.ready;

    var wrapper = container.querySelector(".textarea.richtext");
    var editor = wrapper.editor;

    // the id of the rendered content survived the roundtrip
    expect(editor.getHTML()).toBe('<p data-dbid="1">test</p>');

    // the schema keeps the attribute like the one without addDbids
    // keeps the class attribute
    editor.setHTML('<h1 class="teaser" data-dbid="1">Headline</h1><p data-dbid="2">text <a href="pageref://123" data-dbid="456" class="internal">ref</a></p>');

    var html = editor.getHTML();

    expect(html).toContain("data-dbid=\"1\"");
    expect(html).toContain("data-dbid=\"2\"");
    expect(html).toContain("data-dbid=\"456\"");
    expect(html).toContain("class=\"teaser\"");
    expect(html).toContain("class=\"internal\"");

    // new blocks get a placeholder id, so the server can tell them
    // apart from the stored ones
    editor.setHTML('<p data-dbid="7">hello world</p>');
    editor.editor.commands.setTextSelection(12);
    editor.editor.commands.keyboardShortcut("Enter");

    var paragraphs = wrapper.querySelectorAll("p");

    expect(paragraphs.length).toBe(2);
    expect(paragraphs[0].getAttribute("data-dbid")).toBe("7");
    expect(paragraphs[1].getAttribute("data-dbid")).toMatch(/^-\d+$/);
});
// }}}
// {{{ trailing line breaks
test("tiptap editor removes stray line breaks at the end of paragraphs", async () => {
    // add a second form whose content contains the line breaks the
    // php side adds to empty paragraphs (HtmlDom::cleanHTML())
    document.body.insertAdjacentHTML("beforeend",
        '<form class="depage-form" id="breakForm">' +
        '<p id="breakForm-rt" class="input-richtext" ' +
        'data-textarea-options="{}" ' +
        'data-richtext-options=\'{"allowedTags":["p","br","b","i","a"],"editor":"tiptap"}\'>' +
        '<label>Breaks</label>' +
        '<textarea name="rt"></textarea>' +
        '</p></form>'
    );

    var form = document.getElementById("breakForm");
    var container = form.querySelector(".input-richtext");

    // happy-dom breaks textarea.value for html content (it returns only
    // the text content), so assign the value through the property like a
    // browser would deliver it from the php-rendered, escaped textarea
    container.querySelector("textarea").value =
        "<p>hello<br></p>\n<p><br></p>\n<p>line1<br>line2</p>\n";

    var instance = DepageForm.setupForm(form);
    await instance.ready;

    var wrapper = container.querySelector(".textarea.richtext");
    var editor = wrapper.editor;

    // the trailing breaks are gone, the empty paragraph is still there
    expect(withoutDbids(editor.getHTML())).toBe("<p>hello</p><p></p><p>line1<br>line2</p>");

    // the empty paragraph is rendered by prosemirror itself — its
    // trailing break is not part of the markup
    var paragraphs = wrapper.querySelectorAll("p");

    expect(paragraphs.length).toBe(3);
    expect(paragraphs[1].querySelector(".ProseMirror-trailingBreak")).not.toBeNull();
    expect(paragraphs[1].innerHTML).not.toContain("<br>");

    // the same happens when the content is set afterwards
    editor.setHTML("<p>hello<br></p>");

    expect(withoutDbids(editor.getHTML())).toBe("<p>hello</p>");
});
// }}}
// {{{ data-dbid on split blocks
test("tiptap schema does not copy data-dbid to new blocks after enter", async () => {
    var container = document.querySelector(".input-richtext");
    var wrapper = container.querySelector(".textarea.richtext");
    var editor = wrapper.editor;
    var tiptap = editor.editor;

    editor.setHTML('<p data-dbid="7">hello world</p>');

    // press enter at the end of the paragraph: the new paragraph is a
    // new node and must not reuse the id — otherwise saveNodePatch()
    // would see two elements with the same db:id
    tiptap.commands.setTextSelection(12);
    tiptap.commands.keyboardShortcut("Enter");

    var paragraphs = wrapper.querySelectorAll("p");

    expect(paragraphs.length).toBe(2);
    expect(paragraphs[0].getAttribute("data-dbid")).toBe("7");
    // the new block gets a placeholder id, the server replaces it with
    // the database id it stores the block with
    expect(paragraphs[1].getAttribute("data-dbid")).toMatch(/^-\d+$/);
    expect(editor.getHTML().split("data-dbid=\"7\"").length).toBe(2);

    // pressing enter in the middle of a block inherits the attributes
    // through prosemirror's node.copy(); the duplicate id is replaced by
    // a placeholder id, so that every block keeps an id of its own
    tiptap.commands.setTextSelection(6);
    tiptap.commands.keyboardShortcut("Enter");

    paragraphs = wrapper.querySelectorAll("p");

    expect(paragraphs.length).toBe(3);
    expect(paragraphs[0].getAttribute("data-dbid")).toBe("7");
    expect(paragraphs[1].getAttribute("data-dbid")).toMatch(/^-\d+$/);
    expect(paragraphs[2].getAttribute("data-dbid")).toMatch(/^-\d+$/);
    expect(editor.getHTML().split("data-dbid=\"7\"").length).toBe(2);

    // the placeholder ids are unique
    var ids = Array.from(paragraphs).map((p) => p.getAttribute("data-dbid"));

    expect(new Set(ids).size).toBe(ids.length);
});
// }}}
// {{{ placeholder ids
test("tiptap editor gives new blocks a placeholder id", async () => {
    var container = document.querySelector(".input-richtext");
    var wrapper = container.querySelector(".textarea.richtext");
    var editor = wrapper.editor;

    editor.setHTML('<p data-dbid="7">hello world</p>');

    // a new block the user creates has no id of its own
    editor.editor.commands.setTextSelection(12);
    editor.editor.commands.keyboardShortcut("Enter");
    editor.editor.commands.insertContent("new text");

    var paragraphs = wrapper.querySelectorAll("p");
    var placeholder = paragraphs[1].getAttribute("data-dbid");

    expect(placeholder).toMatch(/^-\d+$/);

    // the placeholder is negative, the database ids are positive
    expect(parseInt(placeholder, 10)).toBeLessThan(0);
});
// }}}
// {{{ placeholder ids of marks
test("tiptap editor gives new marks a placeholder id", async () => {
    var container = document.querySelector(".input-richtext");
    var wrapper = container.querySelector(".textarea.richtext");
    var editor = wrapper.editor;

    editor.setHTML('<p data-dbid="7">hello world</p>');

    // a mark the user applies has no id of its own
    editor.editor.chain().focus().setTextSelection({ from: 1, to: 6 }).toggleBold().run();

    var marks = [];

    editor.editor.state.doc.descendants((node) => {
        node.marks.forEach((mark) => marks.push(mark));
    });

    expect(marks.length).toBe(1);

    var placeholder = marks[0].attrs.dbid;

    expect(placeholder).toMatch(/^-\d+$/);

    // the server reports the database id it stored the mark with
    var form = document.querySelector("form.depage-form");

    form.dispatchEvent(new CustomEvent("depageForm.autosaved", {
        bubbles: true,
        detail: {
            result: { success: true, changed: true, ids: { [placeholder]: 4712 } },
            values: {},
        },
    }));

    marks = [];

    editor.editor.state.doc.descendants((node) => {
        node.marks.forEach((mark) => marks.push(mark));
    });

    expect(marks.length).toBe(1);
    expect(marks[0].attrs.dbid).toBe("4712");
});
// }}}
// {{{ id map of the autosave response
test("tiptap editor replaces the placeholder ids with the database ids", async () => {
    var form = document.querySelector("form.depage-form");
    var container = form.querySelector(".input-richtext");
    var wrapper = container.querySelector(".textarea.richtext");
    var editor = wrapper.editor;

    editor.setHTML('<p data-dbid="7">hello world</p>');

    editor.editor.commands.setTextSelection(12);
    editor.editor.commands.keyboardShortcut("Enter");

    var paragraphs = wrapper.querySelectorAll("p");
    var placeholder = paragraphs[1].getAttribute("data-dbid");

    expect(placeholder).toMatch(/^-\d+$/);

    // the server reports the database id it stored the new block with
    form.dispatchEvent(new CustomEvent("depageForm.autosaved", {
        bubbles: true,
        detail: {
            result: { success: true, changed: true, ids: { [placeholder]: 4711 } },
            values: {},
        },
    }));

    paragraphs = wrapper.querySelectorAll("p");

    expect(paragraphs[0].getAttribute("data-dbid")).toBe("7");
    expect(paragraphs[1].getAttribute("data-dbid")).toBe("4711");

    // the value of the hidden input is updated right away, so that the
    // next save does not send the placeholder again
    var input = container.querySelector("input[type='hidden']");

    expect(input.value).toContain("data-dbid=\"4711\"");
    expect(input.value).not.toContain(placeholder);
});
// }}}
// {{{ format helpers
test("tiptap facade implements the toolbar API", async () => {
    var container = document.querySelector(".input-richtext");
    var wrapper = container.querySelector(".textarea.richtext");
    var editor = wrapper.editor;

    editor.setHTML("<h1>Title</h1><p>paragraph</p>");

    expect(typeof editor.hasFormat).toBe("function");
    expect(typeof editor.changeFormat).toBe("function");
    expect(typeof editor.replaceBlock).toBe("function");
    expect(typeof editor.makeUnorderedList).toBe("function");
    expect(typeof editor.makeOrderedList).toBe("function");
    expect(typeof editor.removeList).toBe("function");
    expect(typeof editor.getPosBySelection).toBe("function");
    expect(typeof editor.showLinkDialog).toBe("function");
    expect(typeof editor.handleTableButtonClick).toBe("function");

    // select the headline and convert it to a paragraph
    var tiptap = editor.editor;
    tiptap.commands.setTextSelection({ from: 1, to: 3 });

    expect(editor.hasFormat("h1")).toBe(true);
    expect(editor.hasFormat("h2")).toBe(false);

    editor.replaceBlock("p");

    expect(withoutDbids(editor.getHTML())).toContain("<p>Title</p>");

    // showLinkDialog dispatches the shared event
    var event = null;
    container.addEventListener("depageForm.showLinkDialog", (e) => {
        event = e;
        e.preventDefault();
    }, { once: true });

    editor.showLinkDialog("https://example.com", () => {});

    expect(event).not.toBeNull();
    expect(event.detail.href).toBe("https://example.com");
    expect(typeof event.detail.callback).toBe("function");
});
// }}}
// {{{ tables
test("tiptap backend parses and edits tables", async () => {
    // add a second form whose allowedTags include tables
    document.body.insertAdjacentHTML("beforeend",
        '<form class="depage-form" id="tableForm">' +
        '<p id="tableForm-tbl" class="input-richtext" ' +
        'data-textarea-options="{}" ' +
        'data-richtext-options=\'{"allowedTags":["table","tbody","tr","td","p","br","b","i","a"],"editor":"tiptap","addDbids":true}\'>' +
        '<label>Table</label>' +
        '<textarea name="tbl"></textarea>' +
        '</p></form>'
    );

    var form = document.getElementById("tableForm");
    var container = form.querySelector(".input-richtext");

    // happy-dom breaks textarea.value for html content (it returns only
    // the text content), so assign the value through the property like a
    // browser would deliver it from the php-rendered, escaped textarea
    container.querySelector("textarea").value =
        "<table data-dbid=\"10\"><tbody><tr data-dbid=\"11\"><td data-dbid=\"12\">a</td><td>b</td></tr><tr><td>c</td><td>d</td></tr></tbody></table>";

    var instance = DepageForm.setupForm(form);
    await instance.ready;

    var wrapper = container.querySelector(".textarea.richtext");
    var editor = wrapper.editor;

    // the table markup survived the schema roundtrip
    var html = editor.getHTML();
    expect(html).toContain("<table");
    expect(html).toContain("data-dbid=\"10\"");
    expect(html).toContain("data-dbid=\"11\"");
    expect(html).toContain("data-dbid=\"12\"");
    expect(html).toContain("<tbody>");
    expect(wrapper.querySelectorAll("tr").length).toBe(2);
    expect(wrapper.querySelectorAll("td").length).toBe(4);

    // add a row through the table commands, like handleTableButtonClick does
    var td = wrapper.querySelector("td");
    var pos = editor.editor.view.posAtDOM(td, 0);

    editor.editor.chain().setTextSelection(pos).addRowAfter().run();

    expect(wrapper.querySelectorAll("tr").length).toBe(3);

    // remove it again
    editor.editor.chain().setTextSelection(pos).deleteRow().run();

    expect(wrapper.querySelectorAll("tr").length).toBe(2);
});
// }}}

/* vim:set ft=javascript sw=4 sts=4 fdm=marker : */
