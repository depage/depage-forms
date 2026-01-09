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
    - client side [(jQuery Tools)](http://flowplayer.org/tools/ "jQuery Tools")
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

- PHP 8.0
- jQuery optional, for client side validation and richtext field)

Introduction
------------

- You can get a basic introduction at:
  [depage-forms: html5 forms made easy (part I)](Docs/Introduction.md)
- How to do Form Validation
  [depage-forms: validation of html5 forms (part II)](Docs/Validation.md)


Install Using Composer
----------------------
Get composer at <https://getcomposer.org> and then just add this to your composer.json.

    composer require depage/htmlform

to install the current version of depage-htmlform into your vendor dir.


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

