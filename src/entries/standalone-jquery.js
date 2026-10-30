/**
 * @file    src/entries/standalone-jquery.js
 * @brief   entry for the jquery iife bundle (dist/depage-form-jquery.js)
 *
 * Registers $.fn.depageForm and auto-initializes all .depage-form
 * forms — drop-in replacement for the old effect.js:
 *
 *     <link rel="stylesheet" href="dist/depage-forms.css">
 *     <script src="jquery.min.js"></script>
 *     <script src="dist/depage-form-jquery.js"></script>
 *
 * jQuery is not bundled — it is expected to be loaded on the page
 * (exposed as the global "jQuery").
 **/
import "../htmlform-jquery.js";

/* vim:set ft=javascript sw=4 sts=4 fdm=marker : */
