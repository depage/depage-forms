/**
 * @file    src/htmlform-jquery.js
 * @brief   jquery compatibility layer for @depage/htmlform
 *
 * Registers the classic $.fn.depageForm() plugin and auto-initializes
 * all .depage-form forms on DOM-ready — so the library can be used
 * "as is" like the old effect.js:
 *
 *     <script src="jquery.min.js"></script>
 *     <script src="dist/depage-form-jquery.js"></script>
 *
 * or as a module:
 *
 *     import "@depage/htmlform/jquery";
 *     $(".depage-form").depageForm();
 *
 * The heavy lifting is done by the jQuery-free core module; this file
 * only bridges the jQuery APIs around it:
 *
 *     - $form.depageForm() sets up the form
 *     - the core dispatches events with dotted names like
 *       "depageForm.autosaved" as native CustomEvents; jQuery treats
 *       the dot as a namespace separator, so this module bridges the
 *       events in both directions:
 *         - $form.on("depageForm.autosaved", ...) handlers fire when
 *           the core dispatches the native event
 *         - $form.trigger("depageForm.beforeAutosave") reaches native
 *           listeners, and isDefaultPrevented() keeps working
 **/
import jQuery from "jquery";
import { setup, setupForm } from "./htmlform.js";

var $ = jQuery;

/* {{{ $.fn.depageForm() */
$.fn.depageForm = function() {
    return this.each(function() {
        setupForm(this);
    });
};
/* }}} */

/* {{{ event bridge between native CustomEvents and jQuery events */
// The core module dispatches events with dotted names like
// "depageForm.autosaved". jQuery interprets the dot as a namespace
// separator and binds such handlers to the base type ("depageForm"),
// so jQuery-bound handlers never see the native events (and vice
// versa). The bridge below re-dispatches in both directions:
//
//     - native dispatch -> jQuery handlers (jQuery.event.trigger does
//       not re-dispatch natively for custom event types)
//     - $().trigger("depageForm.xxx") -> native dispatch, so native
//       listeners and the core module see it as well
var bridgeDepth = 0;
var bridgedEvents = [
    "depageForm.beforeAutosave",
    "depageForm.autosaved",
    "depageForm.autosaveFailed",
    "depageForm.richtextInitialized",
];

// native -> jquery bridge
for (const type of bridgedEvents) {
    document.addEventListener(type, (e) => {
        if (bridgeDepth > 0) return;

        bridgeDepth++;

        // triggers the jQuery handlers bound for this event; the
        // bridgeDepth guard keeps the special.trigger hook below from
        // re-dispatching a native event in turn. The CustomEvent's
        // detail is passed along as the extra handler argument, like
        // the old triggerHandler(type, data) calls did.
        var jqEvent = jQuery.Event(type);
        jQuery.event.trigger(jqEvent, e.detail != null ? [e.detail] : null, e.target);

        bridgeDepth--;

        if (jqEvent.isDefaultPrevented() && e.cancelable) {
            e.preventDefault();
        }
    }, true);
}

// jquery -> native bridge
// Note: jQuery splits "depageForm.autosaved" into the base type
// "depageForm" and the namespace "autosaved" before looking up
// special events — so the hook is registered on the base type and
// rebuilds the full event name from event.namespace.
$.event.special["depageForm"] = {
    trigger: function(event, data) {
        if (bridgeDepth > 0) return;

        var type = "depageForm" + (event && event.namespace ? "." + event.namespace : "");

        if (bridgedEvents.indexOf(type) === -1) {
            // not one of the bridged events -> let jQuery handle it
            return;
        }

        var nativeEvent = new CustomEvent(type, {
            bubbles: true,
            cancelable: true,
            detail: data
        });

        this.dispatchEvent(nativeEvent);

        if (nativeEvent.defaultPrevented && event && typeof event.preventDefault === "function") {
            event.preventDefault();
        }

        // skip jQuery's own trigger processing — handlers already
        // ran through the native -> jquery bridge above
        return false;
    }
};
/* }}} */

/* {{{ auto-init */
$(function() {
    $('.depage-form').depageForm();

    // depage-magaziner: native event
    document.body.addEventListener("depage-magaziner:loaded", function(e) {
        var page = e.detail && e.detail.page ? e.detail.page : e.detail;

        if (page instanceof Element) {
            setup(page);
        }
    });

    // depage-magaziner: legacy jQuery event
    $("body").on("depage.magaziner.loaded", function(_e, _url, page) {
        if (page) {
            setup(page.jquery ? page[0] : page);
        }
    });
});
/* }}} */

export { setup, setupForm };
export default $;

/* vim:set ft=javascript sw=4 sts=4 fdm=marker : */
