/**
 * @file    src/tests/htmlform.test.js
 * @brief   smoke test for the standalone module
 *
 * Renders a real depage-forms form with php and runs the module
 * against it in a happy-dom environment.
 *
 *     bun test src/tests
 **/
import { test, expect, beforeAll } from "bun:test";
import { renderFixture } from "./dom.js";

document.body.innerHTML = renderFixture();

const { setup, default: DepageForm } = await import("../htmlform.js");

var form;
var instance;

beforeAll(() => {
    const instances = setup();

    form = document.querySelector("form.depage-form");
    instance = instances[0];
});

// {{{ setup
test("setup() initializes the form once", () => {
    expect(instance).toBeInstanceOf(DepageForm);
    expect(form.getAttribute("data-depage-form-initialized")).toBe("true");
    expect(form.classList.contains("autosave")).toBe(true);

    // idempotent
    expect(DepageForm.setupForm(form)).toBe(instance);
});
// }}}
// {{{ validation on submit
test("submitting an empty form is prevented and shows the error message", () => {
    var submitEvent = new Event("submit", { bubbles: true, cancelable: true });
    form.dispatchEvent(submitEvent);

    expect(submitEvent.defaultPrevented).toBe(true);

    var usernameParagraph = document.getElementById("testForm-username");

    expect(usernameParagraph.classList.contains("error")).toBe(true);

    var errorMessage = usernameParagraph.querySelector(".errormessage");

    expect(errorMessage).not.toBeNull();
    expect(errorMessage.textContent).toBe("Please enter valid data");
    expect(errorMessage.style.display).toBe("");

    // submit paragraph is marked as error
    expect(form.querySelector(".submit").classList.contains("error")).toBe(true);

    // fail event is dispatched
    var failed = false;
    form.addEventListener("fail", () => { failed = true; }, { once: true });
    form.dispatchEvent(new Event("submit", { bubbles: true, cancelable: true }));
    expect(failed).toBe(true);
});

test("error message is hidden when the input is focused", () => {
    var input = form.querySelector("input[name='username']");
    var errorMessage = document.getElementById("testForm-username").querySelector(".errormessage");

    input.dispatchEvent(new Event("focusin", { bubbles: true }));

    expect(errorMessage.style.display).toBe("none");
});

test("filling the form makes the submit succeed", () => {
    form.querySelector("input[name='username']").value = "depage";
    form.querySelector("input[name='email']").value = "js@depage.net";
    form.querySelector("input[name='accept']").checked = true;
    form.querySelector("input[name='size'][value='s']").checked = true;

    var succeeded = false;
    form.addEventListener("success", () => { succeeded = true; }, { once: true });

    var submitEvent = new Event("submit", { bubbles: true, cancelable: true });
    form.dispatchEvent(submitEvent);

    expect(submitEvent.defaultPrevented).toBe(false);
    expect(succeeded).toBe(true);
    expect(document.getElementById("testForm-username").classList.contains("error")).toBe(false);
    expect(document.getElementById("testForm-username").querySelector(".errormessage")).toBeNull();
    expect(form.querySelector(".submit").classList.contains("error")).toBe(false);
});
// }}}
// {{{ getFormValues
test("getFormValues() collects values like the old implementation", () => {
    form.querySelector("input[name='colors[]'][value='red']").checked = true;
    form.querySelector("input[name='colors[]'][value='blue']").checked = true;

    var data = instance.getFormValues();

    expect(data.username).toBe("depage");
    expect(data.email).toBe("js@depage.net");
    expect(data["colors[]"]).toEqual(["red", "blue"]);
    expect(data.accept).toBe("true");
    expect(data.size).toBe("s");
    expect(data.formName).toBe("testForm");
    expect(data.formAutosave).toBe("true");
});
// }}}
// {{{ autosave
test("changing an input autosaves via fetch", async () => {
    var requests = [];

    globalThis.fetch = async (url, options) => {
        requests.push({ url, options });

        return { ok: true };
    };

    // reset the lastSaved snapshot
    instance.state.lastSaved = instance.getFormValues();

    var input = form.querySelector("input[name='username']");
    input.value = "depage2";
    input.dispatchEvent(new Event("change", { bubbles: true }));

    // wait for the async save
    await new Promise((resolve) => setTimeout(resolve, 10));

    expect(requests.length).toBe(1);
    expect(form.classList.contains("autosave-success")).toBe(true);

    var body = requests[0].options.body.toString();

    expect(body).toContain("username=depage2");
    expect(body).toContain("colors%5B%5D=red");
    expect(body).toContain("colors%5B%5D=blue");
    expect(body).toContain("formAutosave=true");
    expect(requests[0].options.headers["Content-Type"]).toContain("application/x-www-form-urlencoded");

    // submit buttons were re-enabled after saving
    expect(form.querySelector("input[type='submit']").disabled).toBe(false);
});

test("autosave updates the csrf token and retries on failure", async () => {
    var requests = [];
    var tokenInput = form.querySelector(":scope > input[name='formCsrfToken']");

    // the previous test submitted the form successfully — in a real
    // browser the page would be gone by now
    instance.state.submitting = false;
    tokenInput.value = "old-token";

    // response contains the form with a new csrf token
    var newTokenResponse = "<html><body><form id=\"testForm\"><input name=\"formCsrfToken\" value=\"new-token\"></form></body></html>";

    globalThis.fetch = async (url, options) => {
        requests.push({ url, options });

        if (requests.length === 1) {
            return {
                ok: false,
                text: async () => newTokenResponse,
            };
        }

        return { ok: true };
    };

    instance.state.lastSaved = null;

    var failed = false;
    form.addEventListener("depageForm.autosaveFailed", () => { failed = true; }, { once: true });

    await instance.autosave();
    await new Promise((resolve) => setTimeout(resolve, 10));

    expect(requests.length).toBe(2);
    expect(tokenInput.value).toBe("new-token");
    expect(failed).toBe(false);
    expect(form.classList.contains("autosave-success")).toBe(true);
});

test("depageForm.beforeAutosave can cancel the autosave", async () => {
    var requests = [];

    instance.state.submitting = false;

    globalThis.fetch = async (url, options) => {
        requests.push({ url, options });

        return { ok: true };
    };

    instance.state.lastSaved = null;

    var prevent = (e) => e.preventDefault();
    form.addEventListener("depageForm.beforeAutosave", prevent);

    await instance.autosave();

    expect(requests.length).toBe(0);

    form.removeEventListener("depageForm.beforeAutosave", prevent);
});
// }}}
// {{{ validation on change
test("validate-on-change shows and removes errors per input", () => {
    var container = document.createElement("div");

    container.innerHTML = `
        <form class="depage-form" data-jsvalidation="change" data-jsautosave="false">
            <p class="input-email" data-errormessage="Mail is wrong">
                <label><span class="depage-label">Mail</span><input name="mail" type="email" required="required" value=""></label>
            </p>
            <p class="submit"><input type="submit" value="submit"></p>
        </form>`;
    document.body.appendChild(container);

    var changeForm = container.querySelector("form");
    var mail = changeForm.querySelector("input[name='mail']");
    var paragraph = mail.closest("p");

    DepageForm.setupForm(changeForm);

    // empty required input -> error on change
    mail.dispatchEvent(new Event("change", { bubbles: true }));
    expect(paragraph.classList.contains("error")).toBe(true);
    expect(paragraph.querySelector(".errormessage").textContent).toBe("Mail is wrong");

    // invalid email -> still error
    mail.value = "not-a-mail";
    mail.dispatchEvent(new Event("change", { bubbles: true }));
    expect(paragraph.classList.contains("error")).toBe(true);

    // valid email -> error removed
    mail.value = "valid@depage.net";
    mail.dispatchEvent(new Event("change", { bubbles: true }));
    expect(paragraph.classList.contains("error")).toBe(false);
    expect(paragraph.querySelector(".errormessage")).toBeNull();
});
// }}}

/* vim:set ft=javascript sw=4 sts=4 fdm=marker : */
