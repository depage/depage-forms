depage-forms
============

depage-forms is PHP library for HTML form generation with focus on usability.
It is part of [depage-cms] (http://www.depagecms.net/), but it also works as a standalone tool. By abstracting
HTML, browser flaws (duplicate form submissions) and form validation, it
provides a comfortable way to obtain reliable data.

Features
--------

- avert form resubmission
- validation
    - available for standard input elements
    - customizable with regular expressions
- dividing forms into ѕeparate parts
- almost complete HTML5 form functionality
    - attributes : placeholder, autofocus, textbox datalists, title, pattern
    - input elements : boolean (checkbox), email, hidden, multiple (checkbox, select list), number, password, range, search, single (radio, select list), tel, text, textarea, url
- neat return values with appropriate data types
- easy language localisation
- session timeout
- simple creditcard validation (by values)

Prerequisites
-------------
- PHP 5.3

License
-------
[GPL2] (http://www.gnu.org/licenses/gpl-2.0.html)
[MIT] (http://www.opensource.org/licenses/mit-license.php)

