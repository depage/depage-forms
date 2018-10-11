Developer guide
---------------

The idea behind this project is to combine comfortable form-generation and
modern browser functionality with maximum client coverage.

Comfort
-------
- We abstract browser specifics from HTML-forms to provide a clean interface
  to web developers. All configuration is located in one place.

Coverage
--------
- To reach as many users as possible we have included several fallbacks for
  old browsers. Depage-forms mimics validation-behavior of modern browsers
  with JavaScript (client-side) or PHP (server-side).

HTML5
-----
- We follow the HTML5 spec where it's sensible. The only clash so far is
  @link Depage::HtmlForm::Elements::Multiple::htmlInputAttributes checkbox
  validation @endlink .
- We aim to provide as much HTML5 functionality as possible.

Customization
-------------
- Input-elements can be easily modified by overriding the included
element-classes.
- New element-classes are automatically integrated by the autoloader. (They
can be instantiated with @link Depage::HtmlForm::Abstracts::Container::__call
add @endlink (runtime generated methods))

Developer Prerequisites
-----------------------
- PHP 5.3
- PHPUnit 3.5 (to run included unit tests)
- Doxygen 1.7.2 (to generate documentation)

Coding style
------------
Generally, follow PSR-0, PSR-1, PSR-2 coding standard (http://www.php-fig.org)

Deployment
----------
To generate a gzipped release of the library (includes examples):

    $ make release

To generate a gzipped release with the essentials for working environments:

    $ make min

Tests
-----
To run the unit tests:

    $ make test

Documentation
-------------
To generate documentation:

    $ make doc

