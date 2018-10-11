depage-forms: Validation
==========================

Validation of HTML form data
----------------------------

Thanks to the new HTML Standard and the WHATWG Group it is easier than before to have Browsers show standard errors when a user does not provide valid data inside an input element. Unfortunately this only works in newer Browsers, and not in older ones (e.g. Internet Explorer 8).

To make it easier for developers, depage-forms provides an way easy way to add validation to forms that works in all three different places at the same time:

- on the server
- in the browser, based on the new html standard
- in the browser, based on javascript


Standard Input Elements
-----------------------

There are various standard input elements that are part of the HTML standard and will be validated by the browser. E.g. input[type=email] and input[type=url]. Other input elements are presented with a different user interface or as native controls like input[type=number], input[type=range] or input[type=date].

depage-forms uses these inputs but extends and enhances the behaviour in various places.


Required Fields
---------------

The most simple case for validation happens when an input is required:

```php
/*
 * The most simple validation:
 * Setting 'required' so that a user has to input *something*
 */
$form->addText('username', [
    'label' => 'User name',
    'required' => true,
]);
$form->addBoolean('accept', [
    'label' => 'Accept terms and conditions.',
    'required' => true,
]);
```


Validation based on type
------------------------

depage-forms also validates based in the input-type. And you can provide an optional error-message that will be displayed, in case the user does not provide valid data.

```php
/*
 * Validation based on type:
 * The user has to provide a valid email/url for these
 * fields. If the reuqired flag is not set, the user
 * can leave the field empty, but when he fills in
 * something, it has to be a valid email/url.
 */
$form->addEmail('email', [
    'label' => 'Email address',
    'required' => true,
    'errorMessage' => "Please enter a valid Email",
]);
$form->addUrl('website', [
    'label' => 'Website',
    'required' => true,
    'errorMessage' => "Please enter a valid Website address",
]);
```


Validating number with min/max values
-------------------------------------

For numbers there is the special case of minimum and maximum numbers:

```php
/*
 * Number input element:
 * By default, min, max, step values aren't set
 * (Depend on browser, step is usually 1).
 * Returns float value.
 */
$form->addNumber('Number', [
    'min' => 0,
    'max' => 10,
    'step' => 2,
]);
/*
 * Range input element:
 * Same as number input element but with a slider interface.
 */
$form->addRange('Range', [
    'min' => 0,
    'max' => 10,
]);
```


Validation based on regular expressions
---------------------------------------

The next way to validate values are regular expressions. These inputs will be validated on the server and also in newer browsers that understand the pattern-attribute.

```php
/*
 * The validator pattern has to match the complete string (HTML5 spec). Here
 * are some examples:
 */
$form->addText('singleLetter', [
    'label' => 'Single letter',
    'required' => true,
    'validator' => '/[a-zA-Z]/',
]);
$form->addText('lettersAndNumbers', [
    'label' =>'One ore more letters or numbers',
    'required' => true,
    'validator' => '/[a-zA-Z0-9]+/',
]);
```


Validation with closures
------------------------

Instead of validation with a regular expression, you may also provide a closure function to validate the input value. The closure function will only be validated on the server, not on the client.

```php
/*
 * adds a closure validator:
 * attach a function to the input.
 * In this case you must enter "2" oder a string
 * containing "yes" to pass validation.
 */
$form->addText('closure', [
    'label' => 'closure validated',
    'required' => true,
    'validator' => function($value) {
        if ($value == 2) {
            return true;
        } else {
            return strpos($value, "yes") !== false;
        }
    },
]);
```


Difference to the HTML standard
-------------------------------

There is one important case where depage-forms behaves differently from simple html-inputs: Checkboxes.

In HTML5 if a checkbox is required, every required checkbox in a set has to be checked to validate. In depage-form the boolean field works the same way. If it is required, you have to check it.

But if you use the input-multiple with the checkbox skin, the user only has to check one checkbox of a set to pass validation (analogous to the radio-buttons/input-single).


Javascript Validation
---------------------

For Browsers that don't support the direct validation of input fields or to display the error-messages directly, we can activate the validation with javascript. We have to include jQuery, the jquery-tools and the effects.js into the html-code.

The validation is executed on blur by default, which means everytime you leave a specific input element. The user may only submit the form after entering valid data for all fields.

@htmlonly<iframe class="example" seamless="seamless" src="../examples/js-validation.php"></iframe>@endhtmlonly

@include js-validation.php


More complex validation with dependencies
-----------------------------------------

More complex validations with dependencies between different input fiels are possible, but should be done with subclassing the htmlform. These will be a topic for another post.
