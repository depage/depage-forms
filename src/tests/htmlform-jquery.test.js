/**
 * @file    src/tests/htmlform-jquery.test.js
 * @brief   smoke test for the jquery wrapper module
 *
 *     bun test src/tests
 **/
import { test, expect, beforeAll } from "bun:test";
import { renderFixture } from "./dom.js";

document.body.innerHTML = renderFixture();

const jQuery = (await import("jquery")).default;

globalThis.jQuery = jQuery;
globalThis.$ = jQuery;

await import("../htmlform-jquery.js");

var $form;

beforeAll(() => {
    $form = jQuery("form.depage-form");
});

// {{{ plugin
test("$.fn.depageForm is registered and auto-initializes forms", async () => {
    expect(typeof jQuery.fn.depageForm).toBe("function");

    // the document is already loaded — jQuery schedules the ready
    // callback with setTimeout
    await new Promise((resolve) => setTimeout(resolve, 0));

    // auto-init ran on dom-ready
    expect($form.attr("data-depage-form-initialized")).toBe("true");

    // calling it again does not create a second instance
    $form.depageForm();
    expect($form.attr("data-depage-form-initialized")).toBe("true");
});
// }}}
// {{{ events
test("native events reach jquery handlers", async () => {
    var autosaved = null;

    $form.on("depageForm.autosaved", (_e, detail) => {
        autosaved = detail;
    });

    globalThis.fetch = async () => ({ ok: true });

    // trigger an autosave through the public api
    var { default: DepageForm } = await import("../htmlform.js");
    var instance = DepageForm.setupForm($form[0]);

    instance.state.lastSaved = null;
    instance.state.submitting = false;
    await instance.autosave();

    expect(autosaved).not.toBeNull();
    expect(autosaved.response.ok).toBe(true);

    $form.off("depageForm.autosaved");
});

test("$.trigger('depageForm.beforeAutosave') respects preventDefault", () => {
    var form = $form[0];

    var prevent = (e) => e.preventDefault();
    form.addEventListener("depageForm.beforeAutosave", prevent);

    var event = jQuery.Event("depageForm.beforeAutosave");
    $form.trigger(event);

    expect(event.isDefaultPrevented()).toBe(true);

    form.removeEventListener("depageForm.beforeAutosave", prevent);

    // without a preventing listener it is not prevented
    var event2 = jQuery.Event("depageForm.beforeAutosave");
    $form.trigger(event2);

    expect(event2.isDefaultPrevented()).toBe(false);
});
// }}}

/* vim:set ft=javascript sw=4 sts=4 fdm=marker : */
