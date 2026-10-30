/**
 * @file    src/tests/htmlform-richtext.test.js
 * @brief   smoke test for the richtext editor setup
 *
 *     bun test src/tests
 **/
import { test, expect } from "bun:test";
import { renderFixture } from "./dom.js";

document.body.innerHTML = renderFixture("render-richtext-fixture.php");

const { default: DepageForm } = await import("../htmlform.js");

// {{{ richtext setup
test("richtext element is replaced by a squire editor", async () => {
    var form = document.querySelector("form.depage-form");
    var container = form.querySelector(".input-richtext");
    var textarea = container.querySelector("textarea");

    expect(textarea).not.toBeNull();

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

    // the editor instance and toolbar are attached
    var wrapper = container.querySelector(".textarea.richtext");
    expect(wrapper.editor).toBeDefined();
    expect(wrapper.toolbar).toBeDefined();

    // toolbar buttons for the allowed tags were created
    var toolbar = wrapper.toolbar.toolbar;
    expect(toolbar.querySelectorAll("li").length).toBeGreaterThan(0);
    expect(toolbar.querySelector(".format-b")).not.toBeNull();
    expect(toolbar.querySelector(".format-h1")).not.toBeNull();

    // the initialized event was dispatched
    expect(initialized).not.toBeNull();
    expect(initialized.container).toBe(container);
    expect(initialized.editor).toBe(wrapper.editor);
});
// }}}
// {{{ textarea autogrow
test("textarea gets autogrow handler", () => {
    var form = document.querySelector("form.depage-form");
    var container = form.querySelector(".input-textarea");
    var textarea = container.querySelector("textarea");

    expect(textarea).not.toBeNull();
    expect(typeof textarea.autogrow).toBe("function");
    expect(container.querySelector(".textarea-size")).not.toBeNull();
});
// }}}
// {{{ tom-select
test("selects are enhanced with tom-select", async () => {
    var form = document.querySelector("form.depage-form");
    var select = form.querySelector("select");

    expect(select).not.toBeNull();

    // wait for the dynamic tom-select import
    await new Promise((resolve) => setTimeout(resolve, 10));

    expect(select.classList.contains("ts-hidden-accessible")).toBe(true);

    var wrapper = form.querySelector(".ts-wrapper");
    expect(wrapper).not.toBeNull();
    expect(wrapper.querySelector(".ts-control")).not.toBeNull();
});
// }}}

/* vim:set ft=javascript sw=4 sts=4 fdm=marker : */
