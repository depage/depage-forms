/**
 * @file    src/htmlform.js
 * @brief   client-side enhancement for depage-forms: validation, autosave, richtext
 *
 * Standalone ES module — no jQuery required.
 * Uses the native Constraint Validation API instead of the old
 * jQuery-Tools validator.
 *
 * usage:
 *     import DepageForm, { setup, setupForm } from "@depage/htmlform";
 *
 *     // initialize all forms with class "depage-form":
 *     setup();
 *
 *     // or initialize a single form element:
 *     setupForm(formElement);
 *
 * events dispatched on the form element (all bubbling):
 *     - "fail"                              validation failed            detail: { errors }
 *     - "success"                           validation succeeded        detail: { inputs }
 *     - "depageForm.beforeAutosave"         before autosaving (cancelable — call preventDefault() to skip)
 *     - "depageForm.autosaved"              autosave succeeded          detail: { response }
 *     - "depageForm.autosaveFailed"         autosave failed
 *     - "depageForm.richtextInitialized"    richtext editor is set up   detail: { container, editor }
 **/

// {{{ throttle()
/**
 * @brief   throttles a function (replaces lodash/throttle)
 *
 * @param   function func function to throttle
 * @param   number wait minimum time between calls in ms
 * @param   object options { leading: true, trailing: true }
 **/
function throttle(func, wait, options = {}) {
    const leading = options.leading !== false;
    const trailing = options.trailing !== false;

    let timeout = null;
    let lastArgs = null;
    let lastThis = null;
    let lastCallTime = 0;

    function invoke() {
        lastCallTime = Date.now();
        timeout = null;
        func.apply(lastThis, lastArgs);
        lastArgs = lastThis = null;
    }

    return function throttled(...args) {
        var now = Date.now();

        if (!lastCallTime && !leading) {
            lastCallTime = now;
        }
        var remaining = wait - (now - lastCallTime);

        lastArgs = args;
        lastThis = this;

        if (remaining <= 0 || remaining > wait) {
            clearTimeout(timeout);
            invoke();
        } else if (!timeout && trailing) {
            timeout = setTimeout(invoke, remaining);
        }
    };
}
// }}}
// {{{ getScrollParent()
/**
 * @brief   returns the closest scrollable parent element (replaces $.fn.scrollParent)
 *
 * @param   element element element to start from
 * @return  element|document
 **/
function getScrollParent(element) {
    var position = getComputedStyle(element).position;
    var excludeStaticParent = position === "absolute";
    var parent = element.parentElement;

    while (parent) {
        if (excludeStaticParent && getComputedStyle(parent).position === "static") {
            parent = parent.parentElement;

            continue;
        }
        var style = getComputedStyle(parent);

        if (/(auto|scroll)/.test(style.overflow + style.overflowY + style.overflowX)) {
            return parent;
        }
        parent = parent.parentElement;
    }

    return element.ownerDocument || document;
}
// }}}
// {{{ splitTag()
/**
 * @brief   splits a tag-string like "h1.classname" into tag, className and attributes
 **/
function splitTag(tag) {
    var result = /([a-zA-Z0-9]*)(\.(.*))?/.exec(tag);

    var attr = [];

    if (typeof result[3] != 'undefined') {
        attr['class'] = result[3];
    }

    return {
        tag: result[1],
        className: result[3],
        attributes: attr
    };
}
// }}}
// {{{ emit()
/**
 * @brief   dispatches a bubbling CustomEvent and returns it
 **/
function emit(target, type, detail = null, cancelable = false) {
    var event = new CustomEvent(type, {
        bubbles: true,
        cancelable: cancelable,
        detail: detail
    });

    target.dispatchEvent(event);

    return event;
}
// }}}
// {{{ toSearchParams()
/**
 * @brief   converts a getFormValues()-object into URLSearchParams
 *
 * Arrays are appended multiple times under the same name, so a
 * field named "example[]" arrives as an array in PHP — the same
 * wire format that jQuery's $.post() produced.
 **/
function toSearchParams(data) {
    var params = new URLSearchParams();

    for (var [key, value] of Object.entries(data)) {
        if (Array.isArray(value)) {
            for (var v of value) {
                params.append(key, v);
            }
        } else {
            params.append(key, value);
        }
    }

    return params;
}
// }}}
// {{{ getElement()
/**
 * @brief   accepts an element, a jQuery object or a selector and returns an element
 **/
function getElement(el) {
    if (typeof el === "string") {
        return document.querySelector(el);
    } else if (el && el.jquery) {
        return el[0];
    }

    return el;
}
// }}}

// {{{ moveRangeBoundariesDownTree()
/**
 * @brief   moves range boundaries down to the deepest text-nodes
 *
 * taken from the squire API that was public in squire v1
 * (Squire.moveRangeBoundariesDownTree) — used for table cell navigation
 **/
function getLength(node) {
    return node.nodeType === Node.TEXT_NODE ? node.length : node.childNodes.length;
}
function isLeaf(node) {
    return node.nodeType === Node.TEXT_NODE || node.nodeName === "BR" || node.nodeName === "IMG";
}
function moveRangeBoundariesDownTree(range) {
    var startContainer = range.startContainer,
        startOffset = range.startOffset,
        endContainer = range.endContainer,
        endOffset = range.endOffset,
        maySkipBR = true,
        child;

    while (startContainer.nodeType !== Node.TEXT_NODE) {
        child = startContainer.childNodes[startOffset];
        if (!child || isLeaf(child)) {
            break;
        }
        startContainer = child;
        startOffset = 0;
    }
    if (endOffset) {
        while (endContainer.nodeType !== Node.TEXT_NODE) {
            child = endContainer.childNodes[endOffset - 1];
            if (!child || isLeaf(child)) {
                if (maySkipBR && child && child.nodeName === 'BR') {
                    endOffset -= 1;
                    maySkipBR = false;
                    continue;
                }
                break;
            }
            endContainer = child;
            endOffset = getLength(endContainer);
        }
    } else {
        while (endContainer.nodeType !== Node.TEXT_NODE) {
            child = endContainer.childNodes[endOffset];
            if (!child || isLeaf(child)) {
                break;
            }
            endContainer = child;
            endOffset = 0;
        }
    }
    range.setStart(startContainer, startOffset);
    range.setEnd(endContainer, endOffset);
}
// }}}

/* {{{ Toolbar */
/**
 * @brief   toolbar for the richtext editor
 **/
class Toolbar {
    // {{{ constructor()
    constructor(container, options, editor) {
        this.container = container;
        this.editor = editor;
        this.options = options;
        this.visible = false;
        this.hideTimeout = null;
        this.mouseX = null;
        this.scrollParent = null;

        this.onDocumentClick = () => {
            this.hide();
        };
        this.onScrollParentScroll = () => {
            this.hide(true);
        };

        this.toolbar = document.createElement("ul");
        this.toolbar.className = "depageEditorToolbar";
        this.toolbar.addEventListener("click", (e) => {
            e.stopPropagation();
        });
        this.container.addEventListener("click", (e) => {
            e.stopPropagation();
        });
        this.container.addEventListener("mousemove", (e) => {
            this.mouseX = e.offsetX;
        });

        this.tagToFunction = {
            'a': {
                add: () => {
                    editor.showLinkDialog("", (value) => {
                        editor.changeFormat(null, { tag: "a" });
                        if (value != "") {
                            editor.changeFormat({ tag: "a", attributes: { href: value } });
                        }
                        editor.focus();
                    });
                },
                remove: () => { editor.changeFormat(null, { tag: "a" }); }
            },
            'b': "inline",
            'i': "inline",
            'u': "inline",
            's': "inline",
            'small': "inline",
            'sub': "inline",
            'sup': "inline",
            'div': "block",
            'p': "block",
            'h1': "block",
            'h2': "block",
            'h3': "block",
            'h4': "block",
            'h5': "block",
            'h6': "block",
            'ul': {
                add: () => { editor.makeUnorderedList(); },
                remove: () => { editor.removeList(); }
            },
            'ol': {
                add: () => { editor.makeOrderedList(); },
                remove: () => { editor.removeList(); }
            }
        };

        var buttonTags = options.allowedTags.slice();
        var hasOtherBlocks = buttonTags.indexOf("ul") !== -1 ||
            buttonTags.indexOf("ol") !== -1 ||
            buttonTags.indexOf("h1") !== -1 ||
            buttonTags.indexOf("h2") !== -1 ||
            buttonTags.indexOf("h3") !== -1 ||
            buttonTags.indexOf("h4") !== -1 ||
            buttonTags.indexOf("h5") !== -1 ||
            buttonTags.indexOf("h6") !== -1;

        var pindex = buttonTags.indexOf("p");
        if (!hasOtherBlocks && pindex !== -1) {
            buttonTags.splice(pindex, 1);
        }

        for (var i = 0; i < buttonTags.length; i++) {
            this.addButton(buttonTags[i]);
        }
    }
    // }}}
    // {{{ addButton()
    addButton(newtag) {
        var result = splitTag(newtag);

        if (typeof this.tagToFunction[result.tag] !== 'undefined') {
            var className = newtag.replace(".", "-");
            var title = result.tag;
            if (title == "small") {
                title = "s";
            }
            var button = document.createElement("li");
            button.className = "format-" + className;
            button.title = newtag;
            button.textContent = title;

            var editor = this.editor;
            var tagToFunction = this.tagToFunction;

            button.addEventListener("click", () => {
                if (tagToFunction[result.tag] == "inline") {
                    if (editor.hasFormat(newtag)) {
                        editor.changeFormat(null, { tag: newtag });
                    } else {
                        editor.changeFormat({ tag: newtag });
                    }
                } else if (tagToFunction[result.tag] == "block") {
                    if (editor.hasFormat(result.tag, result.attributes)) {
                        editor.replaceBlock("p");
                    } else {
                        editor.replaceBlock(newtag);
                    }
                } else {
                    if (editor.hasFormat(newtag)) {
                        tagToFunction[newtag].remove();
                    } else {
                        tagToFunction[newtag].add();
                    }
                }
                editor.focus();
            });

            this.toolbar.appendChild(button);
        }

        return this;
    }
    // }}}
    // {{{ show()
    show() {
        if (this.toolbar.children.length == 0) return;

        document.body.appendChild(this.toolbar);

        this.visible = true;

        for (var i = 0; i < this.options.allowedTags.length; i++) {
            var tag = this.options.allowedTags[i];
            // class names only contain letters, digits and "-"
            var className = tag.replace(".", "-");
            var result = splitTag(tag);
            var button = this.toolbar.querySelector("li.format-" + className);

            if (button) {
                button.classList.toggle("active", this.editor.hasFormat(result.tag, result.attributes));
            }
        }

        document.removeEventListener("click", this.onDocumentClick);
        document.addEventListener("click", this.onDocumentClick);

        this.scrollParent = getScrollParent(this.container);
        this.scrollParent.removeEventListener("scroll", this.onScrollParentScroll);
        this.scrollParent.addEventListener("scroll", this.onScrollParentScroll);

        return this;
    }
    // }}}
    // {{{ hide()
    hide(immediately) {
        var wait = immediately === true ? 0 : 200;

        this.visible = false;

        clearTimeout(this.hideTimeout);
        this.hideTimeout = setTimeout(() => {
            if (!this.visible) {
                this.toolbar.remove();

                document.removeEventListener("click", this.onDocumentClick);
                if (this.scrollParent) {
                    this.scrollParent.removeEventListener("scroll", this.onScrollParentScroll);
                }
            }
        }, wait);

        return this;
    }
    // }}}
    // {{{ setPositionBySelection()
    setPositionBySelection() {
        var pos = this.editor.getPosBySelection(this.toolbar.offsetHeight * 1.5, this.toolbar.offsetWidth * 0.5);

        this.toolbar.style.top = pos.top + "px";
        this.toolbar.style.left = pos.left + "px";

        return this;
    }
    // }}}
}
/* }}} */

/* {{{ DepageForm */
export class DepageForm {
    // {{{ constructor()
    /**
     * @brief   enhances a depage-form form-element
     *
     * @param   element form form element (or selector / jQuery object)
     **/
    constructor(form) {
        this.form = getElement(form);
        this.check = this.form.getAttribute("data-jsvalidation");
        this.autosaveEnabled = this.form.getAttribute("data-jsautosave") == "true";

        // autosave state
        this.state = {
            hasChanged: false,
            saving: false,
            submitting: false,
            lastsave: Date.now(),
            lastSaved: null,
            saveTimer: null,
            successTimer: null,
        };
        this.saveInterval = 300;
        this.submitTimeout = null;

        // validation state
        this.skipValidation = false;
    }
    // }}}

    /* {{{ form setup */
    // {{{ async init()
    /**
     * @brief   sets up all form enhancements
     *
     * The returned promise is also available as instance.ready.
     **/
    async init() {
        if (this.initializing) return this;
        this.initializing = true;

        this.addErrorHandlers();
        this.addTextareaHandlers();
        this.addFocusClassHandlers();
        this.addUrlHandlers();

        // focus first input with autofocus
        var autofocus = this.form.querySelector("input[autofocus]");
        if (autofocus) {
            autofocus.focus();
        }

        var rtfInputs = this.form.querySelectorAll(".input-richtext");
        for (var i = 0; i < rtfInputs.length; i++) {
            try {
                await this.setupRichtextEditor(rtfInputs[i]);
            } catch (exception) {
                console.warn("depage-forms: could not initialize richtext editor", exception);
            }
        }

        try {
            this.setupSelects();
        } catch (exception) {
            console.warn("depage-forms: could not initialize selects", exception);
        }

        this.attachValidation();

        if (this.autosaveEnabled) {
            this.setupAutosave();
        }

        return this;
    }
    // }}}
    // {{{ addErrorHandlers()
    /**
     * @brief   hides error messages when the input gets focus and
     *          focuses the input when the error message is clicked
     **/
    addErrorHandlers() {
        var form = this.form;

        // hide errormessages on focus (focus does not bubble -> focusin)
        form.addEventListener("focusin", (e) => {
            if (!e.target.matches("input, select, textarea")) return;

            var errorParagraph = e.target.closest(".error");
            var errorMessage = errorParagraph && errorParagraph.querySelector(".errormessage");

            if (errorMessage) {
                errorMessage.style.display = "none";
            }
        });

        // focus input on click on error message
        form.addEventListener("click", (e) => {
            var errorMessage = e.target.closest(".errormessage");

            if (!errorMessage || !form.contains(errorMessage)) return;

            var errorParagraph = errorMessage.closest(".error");
            var input = errorParagraph && errorParagraph.querySelector("input, select, textarea");

            if (input) {
                input.focus();

                try {
                    // try to open select on click
                    input.dispatchEvent(new MouseEvent("mousedown", { bubbles: true, cancelable: true }));
                } catch (exception) {
                }
            }
        });
    }
    // }}}
    // {{{ addTextareaHandlers()
    /**
     * @brief   adds autogrow-behavior to textareas
     **/
    addTextareaHandlers() {
        this.form.querySelectorAll(".input-textarea").forEach((container) => {
            var options = {};

            try {
                options = JSON.parse(container.getAttribute("data-textarea-options")) || {};
            } catch (exception) {
            }

            var textarea = container.querySelector("textarea");

            if (!textarea || !options.autogrow) return;

            var textareaSize = document.createElement("textarea");
            textareaSize.className = "textarea-size";
            textareaSize.style.position = "absolute";
            textareaSize.style.left = "-10000px";
            textareaSize.style.height = "auto";
            textarea.parentElement.appendChild(textareaSize);

            var autogrow = function() {
                textareaSize.value = textarea.value;

                // set new height
                var newHeight = textareaSize.scrollHeight + 30;
                textarea.style.height = newHeight + "px";
            };

            textarea.autogrow = autogrow;

            for (var event of ["keyup", "keydown", "scroll"]) {
                textarea.addEventListener(event, autogrow);
            }

            autogrow();
        });
    }
    // }}}
    // {{{ addFocusClassHandlers()
    /**
     * @brief   adds a focus-class to the fieldset of the focused input
     **/
    addFocusClassHandlers() {
        var selector = "input, textarea, select, .input-richtext .textarea-content";

        this.form.addEventListener("focusin", (e) => {
            if (!e.target.matches(selector)) return;

            var fieldset = e.target.closest("fieldset");

            if (fieldset) fieldset.classList.add("focus");
        });
        this.form.addEventListener("focusout", (e) => {
            if (!e.target.matches(selector)) return;

            var fieldset = e.target.closest("fieldset");

            if (fieldset) fieldset.classList.remove("focus");
        });
    }
    // }}}
    // {{{ addUrlHandlers()
    /**
     * @brief   adds missing "http://" to url inputs
     **/
    addUrlHandlers() {
        this.form.addEventListener("change", (e) => {
            var input = e.target;

            if (input.matches && input.matches("input[type='url']") &&
                input.value !== "" &&
                !input.value.match(/[a-z][\w\-]+:\/\/.*/)
            ) {
                input.value = "http://" + input.value;
            }
        });
    }
    // }}}
    /* }}} */

    /* {{{ validation */
    // {{{ getInputs()
    /**
     * @brief   returns the inputs of the form that take part in validation
     *
     * Skips buttons and disabled elements and filters duplicate elements
     * by name (radio/checkbox-groups are validated once per name) —
     * same behavior as the old jQuery-Tools validator.
     *
     * @return  array input elements
     **/
    getInputs() {
        var inputs = [];
        var names = {};

        for (var el of this.form.elements) {
            if (el.tagName !== "INPUT" && el.tagName !== "SELECT" && el.tagName !== "TEXTAREA") continue;
            if (el.disabled) continue;

            if (el.name && names[el.name]) continue;
            names[el.name] = true;

            inputs.push(el);
        }

        return inputs;
    }
    // }}}
    // {{{ getErrorMessageFor()
    /**
     * @brief   returns the error message for an invalid input
     *
     * Prefers the message rendered by depage-forms (data-errormessage
     * attribute of the surrounding paragraph) and falls back to the
     * browser validation message.
     **/
    getErrorMessageFor(input) {
        var paragraph = input.closest("p");
        var message = paragraph && paragraph.getAttribute("data-errormessage");

        return message || input.validationMessage;
    }
    // }}}
    // {{{ showErrors()
    /**
     * @brief   shows error messages for invalid inputs (the old "depageEffect")
     *
     * Adds the error-class to the paragraph of the input and appends an
     * errormessage-span with the text of the data-errormessage attribute.
     **/
    showErrors(errors) {
        for (var { input } of errors) {
            var errorParagraph = input.closest("p");

            if (!errorParagraph) continue;

            var errorMessage = errorParagraph.querySelector(".errormessage");

            if (!errorMessage) {
                errorMessage = document.createElement("span");
                errorMessage.className = "errormessage";
                errorMessage.textContent = this.getErrorMessageFor(input);

                errorParagraph.appendChild(errorMessage);
            } else {
                errorMessage.style.display = "";
            }

            errorParagraph.classList.add("error");
        }
    }
    // }}}
    // {{{ hideErrors()
    /**
     * @brief   removes error messages of inputs that turned valid
     **/
    hideErrors(inputs) {
        for (var input of inputs) {
            var inputParagraph = input.closest("p");

            if (!inputParagraph) continue;

            inputParagraph.classList.remove("error");
            inputParagraph.querySelectorAll(".errormessage").forEach((el) => {
                el.remove();
            });
        }
    }
    // }}}
    // {{{ checkValidity()
    /**
     * @brief   validates the given inputs or all inputs of the form
     *
     * @param   array els input elements to validate (optional)
     * @return  bool true when all given inputs are valid
     **/
    checkValidity(els) {
        els = els || this.getInputs();

        if (!els.length) return true;

        var errors = [];

        for (var el of els) {
            if (!el.checkValidity()) {
                errors.push({ input: el, messages: [this.getErrorMessageFor(el)] });
            }
        }

        if (errors.length) {
            this.showErrors(errors);
            emit(this.form, "fail", { errors: errors });

            return false;
        }

        this.hideErrors(els);
        emit(this.form, "success", { inputs: els });

        return true;
    }
    // }}}
    // {{{ getGroupOf()
    /**
     * @brief   returns all inputs of a radio/checkbox-group by name
     **/
    getGroupOf(input) {
        if (!input.name) return [input];

        return Array.from(this.form.querySelectorAll(
            "input[name=\"" + CSS.escape(input.name) + "\"], select[name=\"" + CSS.escape(input.name) + "\"]"
        ));
    }
    // }}}
    // {{{ attachValidation()
    /**
     * @brief   attaches js-validation to the form
     *
     * Validation mode is read from the data-jsvalidation attribute:
     *     - "blur"   validate each input when it loses focus
     *     - "change" validate each input when it changes
     *     - "submit" only validate when the form is submitted
     *
     * The browser default validation UI is disabled (novalidate), the
     * errors are displayed by showErrors() instead.
     **/
    attachValidation() {
        var form = this.form;
        var check = this.check;

        if (check != 'blur' && check != 'change' && check != 'submit') return;

        // disable browser default validation ui
        form.setAttribute("novalidate", "novalidate");

        // validate on blur or change
        if (check == 'blur' || check == 'change') {
            var inputEvent = check == "blur" ? "focusout" : "change";

            form.addEventListener(inputEvent, (e) => {
                var input = e.target;

                if (!input.matches("input, select, textarea") || input.disabled) return;

                // validate radio/checkbox-groups by name
                if (input.type == "radio" || input.type == "checkbox") {
                    this.checkValidity(this.getGroupOf(input));
                } else {
                    this.checkValidity([input]);
                }
            });
        }

        // cancel/back buttons submit without validation
        form.querySelectorAll("p.cancel input[type='submit'], p.back input[type='submit']").forEach((button) => {
            button.addEventListener("click", () => {
                this.skipValidation = true;
            });
        });

        // validate on submit
        form.addEventListener("submit", (e) => {
            if (this.skipValidation) return;

            if (!this.checkValidity()) {
                e.preventDefault();
            }
        });

        // mark submit-button as erroneous while there are error messages
        form.addEventListener("fail", () => {
            form.querySelectorAll(".submit").forEach((el) => {
                el.classList.add("error");
            });
        });
        form.addEventListener("success", () => {
            // count error messages, because success is called for every
            // successfully validated input
            var errNum = form.querySelectorAll(".errormessage").length;

            form.querySelectorAll(".submit").forEach((el) => {
                el.classList.toggle("error", errNum > 0);
            });
        });
    }
    // }}}
    /* }}} */

    /* {{{ autosave */
    // {{{ getFormValues()
    /**
     * @brief   collects the current values of all form inputs
     *
     * Same semantics as the old implementation: inputs inside a
     * .cancel-paragraph are skipped, checkboxes inside .input-boolean
     * paragraphs are single values, other checkboxes are collected
     * as arrays.
     *
     * @return  object form values + formAutosave=true
     **/
    getFormValues() {
        var data = {};

        this.form.querySelectorAll("input, select, textarea").forEach((el) => {
            if (el.parentElement && el.parentElement.classList.contains("cancel")) return;
            if (el.name == "") return;

            var type = el.type;

            if (type == "radio") {
                if (el.checked) {
                    data[el.name] = el.value;
                }
            } else if (type == "checkbox") {
                var paragraph = el.closest("p");

                if (paragraph && paragraph.classList.contains("input-boolean")) {
                    if (el.checked) {
                        data[el.name] = el.value;
                    }
                } else {
                    if (el.checked) {
                        data[el.name] = data[el.name] || [];
                        data[el.name].push(el.value);
                    }
                }
            } else if (el.tagName === "SELECT" && el.multiple) {
                data[el.name] = Array.from(el.selectedOptions).map((option) => option.value);
            } else {
                data[el.name] = el.value;
            }
        });

        data.formAutosave = "true";

        return data;
    }
    // }}}
    // {{{ setupAutosave()
    /**
     * @brief   sets up autosaving of the form
     **/
    setupAutosave() {
        var form = this.form;

        form.classList.add("autosave");

        this.state.lastSaved = this.getFormValues();

        // normal inputs
        form.addEventListener("change", (e) => {
            if (e.target.matches("input, select, textarea")) {
                this.changed(e.target.type != "hidden");
            }
        });
        // only for inputs and textareas
        form.addEventListener("keyup", (e) => {
            if (!e.target.matches("input, textarea")) return;
            // ignore tom-select's search input
            if (e.target.closest(".ts-wrapper")) return;

            this.changed();
        });

        form.addEventListener("submit", (e) => {
            clearTimeout(this.submitTimeout);

            if (this.state.saving) {
                // form is still saving -> do not submit
                e.preventDefault();

                this.submitTimeout = setTimeout(() => {
                    form.requestSubmit();
                }, 100);

                return;
            }

            // set submitting so the form is not autosaved anymore
            this.state.submitting = true;
        });
    }
    // }}}
    // {{{ changed()
    /**
     * @brief   called when the form content changed — starts autosaving
     *
     * @param   bool saveImmediately save without waiting for the save-interval
     **/
    changed(saveImmediately) {
        this.state.hasChanged = true;

        clearTimeout(this.state.saveTimer);

        if (!this.state.saving &&
            (saveImmediately || Date.now() - this.state.lastsave > this.saveInterval)
        ) {
            this.autosave();

            return;
        }

        this.state.saveTimer = setTimeout(() => {
            this.changed();
        }, this.saveInterval);
    }
    // }}}
    // {{{ autosave()
    /**
     * @brief   saves the form data via POST against the form action
     *
     * On failure the response is parsed as HTML: if it contains a new
     * CSRF-token (e.g. because the session expired) the token is updated
     * and the save is retried once.
     **/
    async autosave() {
        var form = this.form;

        // trigger event before autosaving — cancelable
        var beforeEvent = emit(form, "depageForm.beforeAutosave", null, true);
        if (beforeEvent.defaultPrevented) return;

        var data = this.getFormValues();

        if (JSON.stringify(data) === JSON.stringify(this.state.lastSaved)) {
            // data is the same -> skip save
            return;
        }

        form.classList.remove("autosave-success");
        form.classList.add("autosaving");
        this.state.saving = true;
        this.state.hasChanged = false;

        var submitButtons = form.querySelectorAll("input[type='submit'], button[type='submit']");

        submitButtons.forEach((button) => {
            button.setAttribute("data-was-disabled", button.disabled ? "true" : "false");
            button.disabled = true;
        });

        clearTimeout(this.state.successTimer);

        try {
            var response = await fetch(form.action, {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
                body: toSearchParams(data),
            });

            if (!response.ok) {
                throw response;
            }

            this.state.lastsave = Date.now();
            this.state.lastSaved = data;

            form.classList.add("autosave-success");
            form.classList.remove("autosave-failed");
            emit(form, "depageForm.autosaved", { response: response });

            this.state.successTimer = setTimeout(() => {
                form.classList.remove("autosave-success");
            }, 1000);
        } catch (error) {
            if (this.state.submitting) {
                // form is submitted through default action
                return;
            }

            var retried = false;

            try {
                var text = (error && typeof error.text == "function") ? await error.text() : "";
                var doc = new DOMParser().parseFromString(text, "text/html");
                var i1 = form.querySelector(":scope > input[name='formCsrfToken']");
                var i2 = doc.querySelector("#" + CSS.escape(form.id) + " > input[name='formCsrfToken']");

                if (i1 && i2 && i1.value != i2.value) {
                    // set new csrf token
                    i1.value = i2.value;

                    this.autosave();

                    retried = true;
                }
            } catch (exception) {
            }

            if (!retried) {
                form.classList.add("autosave-failed");
                form.classList.remove("autosave-success");
                emit(form, "depageForm.autosaveFailed");
            }
        } finally {
            this.state.saving = false;
            form.classList.remove("autosaving");

            submitButtons.forEach((button) => {
                button.disabled = button.getAttribute("data-was-disabled") == "true";
            });
        }
    }
    // }}}
    /* }}} */

    /* {{{ richtext */
    // {{{ setupRichtextEditor()
    /**
     * @brief   replaces a textarea with a squire richtext editor
     *
     * dompurify and squire-rte are imported dynamically, so they are
     * only loaded when the form actually contains a richtext element.
     *
     * @param   element container .input-richtext paragraph
     **/
    async setupRichtextEditor(container) {
        var autosave = this.autosaveEnabled;
        var form = this.form;

        const [{ default: DOMPurify }, { default: Squire }] = await Promise.all([
            import("dompurify"),
            import("squire-rte"),
        ]);

        this.patchSquire(Squire);

        // @todo merge option arrays
        var options = {};

        try {
            Object.assign(
                options,
                JSON.parse(container.getAttribute("data-textarea-options") || "{}"),
                JSON.parse(container.getAttribute("data-richtext-options") || "{}")
            );
        } catch (exception) {
        }

        var textarea = container.querySelector("textarea");

        if (!textarea) return;

        var maxlength = parseInt(textarea.getAttribute("maxlength")) || -1;

        var wrapper = document.createElement("div");
        wrapper.className = "textarea richtext";

        var div = document.createElement("div");
        div.className = "textarea-content";
        wrapper.appendChild(div);

        textarea.after(wrapper);

        var input = document.createElement("input");
        input.type = "hidden";
        input.name = textarea.name;
        input.value = textarea.value; // old textarea value
        textarea.after(input);

        var allowedTags = [];

        for (var i = 0; i < (options.allowedTags || []).length; i++) {
            allowedTags.push(splitTag(options.allowedTags[i]).tag);
        }

        var editor = new Squire(div, {
            blockTag: 'P',
            sanitizeToDOMFragment: function(html, _self) {
                var frag = DOMPurify.sanitize(html, {
                    ALLOWED_TAGS: allowedTags,
                    ALLOWED_ATTR: ['class', 'href', 'target', 'alt', 'title', 'data-dbid'],
                    WHOLE_DOCUMENT: false,
                    RETURN_DOM: true,
                    RETURN_DOM_FRAGMENT: true,
                    ALLOWED_URI_REGEXP: /^(?:(?:(?:f|ht)tps?|mailto|tel|callto|cid|xmpp|pageref|libref|footnote):|[^a-z]|[a-z+.\-]+(?:[^a-z+.\-:]|$))/i
                });
                // remove br's that are the last children in a node
                var brs = frag.querySelectorAll('br');
                brs.forEach(function(br) {
                    if (br.nextSibling === null) {
                        br.parentNode.removeChild(br);
                    }
                });

                return frag;
            }
        });

        editor.setHTML(textarea.value);

        textarea.remove();

        var toolbar = new Toolbar(container, options, editor);
        var scrollTop = 0;

        wrapper.editor = editor;
        wrapper.toolbar = toolbar;

        editor.addEventListener("input", throttle(() => {
            input.value = editor.getHTML();
            input.dispatchEvent(new Event("change", { bubbles: true }));

            if (autosave) {
                this.changed();
            }

            toolbar.hide();
        }, 1000, {
            leading: true,
            trailing: true
        }));
        editor.addEventListener("focus", () => {
            wrapper.classList.add("focus");
            wrapper.scrollTop = scrollTop;
        });
        editor.addEventListener("blur", () => {
            input.value = editor.getHTML();
            input.dispatchEvent(new Event("change", { bubbles: true }));

            if (autosave) {
                this.changed();
            }

            wrapper.classList.remove("focus");

            toolbar.hide();
        });
        editor.addEventListener("select", () => {
            if (editor._isFocused) {
                var t = toolbar.show();
                if (t) t.setPositionBySelection();
            }
        });
        editor.addEventListener("keydown", (e) => {
            // @todo fix table selection and navigation on safari
            // Tab — or "^" (keycode 94 with the old keypress event)
            if (e.key != "Tab" && e.key != "^") return;

            var range = editor.getSelection();
            moveRangeBoundariesDownTree(range);

            var node = range.startContainer;
            var cell = (node.nodeType === Node.TEXT_NODE ? node.parentElement : node);
            cell = cell && cell.closest ? cell.closest("td") : null;

            if (!cell || !div.contains(cell)) return;

            var cells = Array.from(div.querySelectorAll("table td"));
            var cellIndex = cells.indexOf(cell);

            if (e.shiftKey) {
                // backwards
                cellIndex--;
            } else {
                // forward
                cellIndex++;
            }

            if (cellIndex < 0 || cellIndex >= cells.length) return;

            var r = document.createRange();
            if (cells[cellIndex].firstChild == null) {
                cells[cellIndex].appendChild(document.createTextNode(""));
            }
            r.setStartBefore(cells[cellIndex].firstChild);
            r.setEndAfter(cells[cellIndex]);

            editor.setSelection(r);
            e.stopPropagation();
            e.preventDefault();
        });

        div.addEventListener("dblclick", (e) => {
            var a = e.target.closest("a");

            if (!a) return;

            setTimeout(function() {
                var r = document.createRange();
                r.selectNode(a);
                editor.setSelection(r);
                editor.showLinkDialog(a.getAttribute("href"), function(value) {
                    editor.changeFormat(null, { tag: "a" });
                    if (value != "") {
                        editor.changeFormat({ tag: "a", attributes: { href: value } });
                    }
                    editor.focus();
                });
            }, 50);
        });
        div.addEventListener("click", (e) => {
            var cell = e.target.closest("table td:last-child");

            if (cell && div.contains(cell)) {
                editor.handleTableButtonClick.call(editor, e, cell);
            }
        });

        wrapper.addEventListener("scroll", () => {
            scrollTop = wrapper.scrollTop;
        });

        if (maxlength > 0) {
            var isMac = /Mac OS X/.test(navigator.userAgent);
            var remaining = document.createElement("span");
            remaining.className = "text-remaining";
            wrapper.appendChild(remaining);

            var updateRemaining = function() {
                var length = div.textContent.length;

                remaining.textContent = (maxlength - length) + " / " + maxlength;
                remaining.classList.toggle("error", length > maxlength);

                return length;
            };

            updateRemaining();

            editor.addEventListener("keydown", (e) => {
                var keycode = e.keyCode;

                //List of keycodes of printable characters from:
                //http://stackoverflow.com/questions/12467240/determine-if-javascript-e-keycode-is-a-printable-non-control-character
                var printable =
                    (keycode > 47 && keycode < 58)   || // number keys
                    keycode == 32 || keycode == 13   || // spacebar & return key(s) (if you want to allow carriage returns)
                    (keycode > 64 && keycode < 91)   || // letter keys
                    (keycode > 95 && keycode < 112)  || // numpad keys
                    (keycode > 185 && keycode < 193) || // ;=,-./` (in order)
                    (keycode > 218 && keycode < 223);   // [\]' (in order)

                // @todo check if text is selected because it would be overridden
                var shortcutKey = (isMac && e.metaKey) || e.ctrlKey;
                var length = updateRemaining();

                if (!shortcutKey && printable) {
                    if (length >= maxlength) {
                        e.preventDefault();
                    }
                }
            });
            for (var event of ["keyup", "contextmenu", "input"]) {
                editor.addEventListener(event, updateRemaining);
            }
        }

        emit(form, "depageForm.richtextInitialized", { container: container, editor: editor });
    }
    // }}}
    // {{{ patchSquire()
    /**
     * @brief   adds depage helpers to the Squire prototype (once)
     **/
    patchSquire(Squire) {
        if (Squire.__depagePatched) return;
        Squire.__depagePatched = true;

        // {{{ Squire.replaceBlock
        Squire.prototype.replaceBlock = function(tag) {
            var result = splitTag(tag);

            this.modifyBlocks(function(frag) {
                var newFrag = frag.ownerDocument.createDocumentFragment();
                var i, l;

                for (i = 0, l = frag.children.length; i < l; i += 1) {
                    var el = frag.ownerDocument.createElement(result.tag);
                    while (frag.children[i].childNodes.length > 0) {
                        var node = frag.children[i].childNodes[0];
                        if (node.nodeName == "LI") {
                            // if element is a list add it's children instead
                            while (node.childNodes.length > 0) {
                                el.appendChild(node.childNodes[0]);
                            }
                            node.parentNode.removeChild(node);
                        } else {
                            el.appendChild(node);
                        }
                    }
                    newFrag.appendChild(el);
                }

                if (typeof result.className != 'undefined') {
                    for (i = 0; i < newFrag.children.length; i++) {
                        newFrag.children[i].className = result.className;
                    }
                }

                return newFrag;
            }, this.getSelection());

            return this.focus();
        };
        // }}}
        // {{{ Squire.makeHeadline
        Squire.prototype.makeHeadline = function(level) {
            if (typeof level == 'undefined') {
                level = 1;
            }
            return this.replaceBlock("h" + level);
        };
        // }}}
        // {{{ Squire.removeHeadline
        Squire.prototype.removeHeadline = function() {
            return this.replaceBlock("p");
        };
        // }}}
        // {{{ Squire.getPosBySelection()
        Squire.prototype.getPosBySelection = function(offsetTop, offsetLeft) {
            var range = this.getSelection();
            var rects = range.getClientRects();
            var scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
            var scrollLeft = document.documentElement.scrollLeft || document.body.scrollLeft;

            offsetTop = offsetTop || 0;
            offsetLeft = offsetLeft || 0;

            // @todo check if mouse position is inside rect and position the toolbar next to mouse
            // @todo check that toolbar stays in visible area of window
            // @todo change position only if necessary (if selection has changed)
            // @todo adjust toolbar position on iOS because of native popup

            var left = 1000000000;
            var right = 0;
            var top = 0;
            for (var i = 0; i < rects.length; i++) {
                left = Math.min(left, rects[i].left);
                right = Math.max(right, rects[i].right);
            }
            if (rects[0]) {
                top = rects[0].top + scrollTop - offsetTop;
            }
            left = left + (right - left) * 0.5 + scrollLeft - offsetLeft;

            if (top < 60) {
                top = 60;
            }

            return {
                top: top,
                left: left
            };
        };
        // }}}
        // {{{ Squire.showLinkDialog()
        if (typeof Squire.prototype.showLinkDialog === 'undefined') {
            Squire.prototype.showLinkDialog = function(_href, _callback) {
                // @todo implement fallback -> alert?
            };
        }
        // }}}
        // {{{ Squire.handleTableButtonClick()
        Squire.prototype.handleTableButtonClick = function(e, cell) {
            cell = cell || e.currentTarget;

            if (e.offsetX < cell.offsetWidth) return;

            var currentRow = cell.parentElement;

            if (e.offsetY < 20) {
                var newRow = currentRow.cloneNode(true);

                Array.from(newRow.children).forEach((td) => {
                    td.textContent = "";

                    var p = td.ownerDocument.createElement("p");
                    p.appendChild(td.ownerDocument.createElement("br"));
                    td.appendChild(p);
                });

                currentRow.after(newRow);
            } else if (e.offsetY < 40 && currentRow.parentElement.children.length > 1) {
                currentRow.remove();
            }

            return false;
        };
        // }}}
    }
    // }}}
    // {{{ setupSelects()
    /**
     * @brief   enhances selects with tom-select
     *
     * tom-select is imported dynamically, so it is only loaded when
     * the form actually contains a select-element. Starts synchronously
     * so the rest of the setup is not delayed by an extra microtask.
     **/
    setupSelects() {
        var selects = this.form.querySelectorAll("select");

        if (selects.length == 0) return;

        import("tom-select").then(({ default: TomSelect }) => {
            selects.forEach((select) => {
                var maxItems = select.getAttribute("data-max-items");

                new TomSelect(select, {
                    maxItems: maxItems === null ? null : parseInt(maxItems),
                    plugins: ['remove_button'],
                    persist: false
                });
            });
        }).catch((exception) => {
            console.warn("depage-forms: could not initialize tom-select", exception);
        });
    }
    // }}}
    /* }}} */
}
/* }}} */

// {{{ instances
var instances = new WeakMap();
// }}}
// {{{ registerBeforeUnload()
var beforeUnloadRegistered = false;

/**
 * @brief   saves all autosave-forms before the page is unloaded
 *
 * Warns the user when a form is still saving.
 **/
function registerBeforeUnload() {
    if (beforeUnloadRegistered) return;
    beforeUnloadRegistered = true;

    window.addEventListener("beforeunload", (e) => {
        var isSaving = false;

        document.querySelectorAll("form.depage-form").forEach((form) => {
            var instance = instances.get(form);

            if (instance && instance.autosaveEnabled && !instance.state.submitting) {
                instance.autosave();

                if (instance.state.saving) {
                    isSaving = true;
                }
            }
        });

        if (isSaving) {
            e.preventDefault();

            // needs to be set for some browsers to show the dialog
            e.returnValue = "The form is still saving. You really want to leave this page?";
        }
    });
}
// }}}
// {{{ setupForm()
/**
 * @brief   initializes a single form (idempotent)
 *
 * @param   element form form element, selector or jQuery object
 * @return  DepageForm
 **/
export function setupForm(form) {
    form = getElement(form);

    if (!form) return null;
    if (instances.has(form)) return instances.get(form);

    var instance = new DepageForm(form);

    instances.set(form, instance);
    form.setAttribute("data-depage-form-initialized", "true");

    registerBeforeUnload();

    instance.ready = instance.init();

    return instance;
}
// }}}
// {{{ setup()
/**
 * @brief   initializes all .depage-form forms inside root (idempotent)
 *
 * @param   element root element, selector or jQuery object (optional, defaults to document)
 * @return  array DepageForm instances
 **/
export function setup(root) {
    root = root ? getElement(root) : document;

    var forms = root.matches && root.matches(".depage-form")
        ? [root]
        : Array.from(root.querySelectorAll(".depage-form"));

    return forms.map((form) => setupForm(form));
}
// }}}

DepageForm.setup = setup;
DepageForm.setupForm = setupForm;

export default DepageForm;

/* vim:set ft=javascript sw=4 sts=4 fdm=marker : */
