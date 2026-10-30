HTML5-forms in PHP with ease and comfort
========================================

depage-forms is PHP library for HTML form generation with focus on usability.
It is part of the upcoming version of [depage-cms](http://www.depagecms.net),
but it also works as a standalone library. By abstracting HTML, browser flaws
(duplicate form submissions) and form validation, it provides a comfortable
way to obtain reliable and validated data from users.


Features
--------

- validation
    - server side
    - client side (native browser validation, no dependencies)
    - available for standard input elements
    - customizable with regular expressions
    - basic CSRF protection
- HTML5 form features
    - attributes : placeholder, autofocus, textbox datalists, title, pattern
    - input elements : boolean (checkbox), email, hidden, multiple (checkbox, select list), number, password, range, search, single (radio, select list), tel, text, textarea, url
- richtext element (wysiwyg HTML editor)
- averts form resubmission
- divide forms into ѕeparate parts
- neat return values with appropriate data types
- easy language localisation
- session timeout
- simple creditcard validation (by values)
- unit tested

Prerequisites
-------------

- PHP 8.3
- a modern browser for the client side enhancement (evergreen browsers)
- jQuery is optional and only needed for the legacy jQuery plugin API

Introduction
------------

- You can get a basic introduction at:
  [depage-forms: html5 forms made easy (part I)](Docs/Introduction.md)
- How to do Form Validation
  [depage-forms: validation of html5 forms (part II)](Docs/Validation.md)


Custom HTML and Security
------------------------

You can add custom HTML in between the form elements with `addHtml()`.
The HTML is printed as-is, so you should only add HTML that you control
yourself:

```php
$form->addHtml('<div class="note">Custom <b>HTML</b> element</div>');
```

If you want to add HTML from an untrusted source, e.g. HTML that was
entered by a user, use `addSanitizedHtml()` instead. It removes all tags
and attributes that are not explicitly allowed before rendering:

```php
// sanitize with the default list of allowed tags:
// p, br, h1, h2, ul, ol, li, a, b, strong, i, em
$form->addSanitizedHtml($userHtml);

// or pass your own list of allowed tags
// (add ".classname" to only allow specific classes on a tag)
$form->addSanitizedHtml($userHtml, ['p', 'br', 'a', 'b', 'i', 'em', 'div.note']);
```

Alternatively you can pass the sanitize-parameter to `addHtml()`:

```php
$form->addHtml($userHtml, ['sanitize' => true]);
```


Install Using Composer
----------------------
Get composer at <https://getcomposer.org> and then just add this to your composer.json.

    composer require depage/htmlform

to install the current version of depage-htmlform into your vendor dir.


Client Side JavaScript
----------------------

The JavaScript that enhances the forms (client side validation, autosave
and richtext editing) is provided as ES modules and as prebuilt bundles
in `dist/`. It does not need jQuery.

With a script tag (bundles from `dist/`):

```html
<link rel="stylesheet" href="dist/depage-forms.css">
<script src="dist/depage-form.js"></script>
```

The bundle auto-initializes all forms with the class `depage-form`.

As an ES module, e.g. with npm:

```bash
npm install @depage/htmlform
```

```js
import DepageForm, { setup, setupForm } from "@depage/htmlform";
import "@depage/htmlform/sass/depage-forms.scss"; // or link dist/depage-forms.css

// initialize all forms with class "depage-form":
setup();

// or a single form element:
setupForm(formElement);
```

If you want to keep using the classic jQuery plugin API
(`$(".depage-form").depageForm()`), use the jQuery build instead:

```html
<script src="jquery.min.js"></script>
<script src="dist/depage-form-jquery.js"></script>
```

```js
import "@depage/htmlform/jquery";
```

The form events (`depageForm.beforeAutosave`, `depageForm.autosaved`,
`depageForm.autosaveFailed`, `depageForm.richtextInitialized`) are
dispatched as native `CustomEvent`s; with the jQuery build they are
bridged, so `$form.on()`/`$form.trigger()` keep working as well.

To rebuild the bundles and the css from `src/` yourself:

```bash
bun install
bun run build
```


For more information
--------------------

- You can fork us at:
  <https://github.com/depage/depage-forms/>
- You find the documentation at:
  <https://docs.depage.net/depage-forms/>
- For more information about depage-cms go to:
  <http://www.depagecms.net/>
- if you want to support with development
  [More info](Docs/Developer.md)

License (dual)
--------------

- GPL2: <https://www.gnu.org/licenses/gpl-2.0.html>
- MIT: <https://www.opensource.org/licenses/mit-license.php>

