/**
 * @file    src/entries/standalone.js
 * @brief   entry for the standalone iife bundle (dist/depage-form.js)
 *
 * Auto-initializes all .depage-form forms and exposes the api as
 * window.depageForm, so the bundle can be used with a plain script tag:
 *
 *     <link rel="stylesheet" href="dist/depage-forms.css">
 *     <script src="dist/depage-form.js"></script>
 **/
import DepageForm, { setup, setupForm } from "../htmlform.js";

function ready(fn) {
    if (document.readyState !== "loading") {
        fn();
    } else {
        document.addEventListener("DOMContentLoaded", fn);
    }
}

ready(function() {
    setup();

    // depage-magaziner support
    document.body.addEventListener("depage-magaziner:loaded", function(e) {
        var page = e.detail && e.detail.page ? e.detail.page : e.detail;

        if (page instanceof Element) {
            setup(page);
        }
    });
});

window.depageForm = {
    DepageForm: DepageForm,
    setup: setup,
    setupForm: setupForm,
};

/* vim:set ft=javascript sw=4 sts=4 fdm=marker : */
