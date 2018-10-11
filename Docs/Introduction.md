depage-forms: Introduction
==========================

HTML forms: Easy and difficult at the same time
-----------------------------------------------

HTML-Forms are simple to write – but to do it in a good way and to make them comfortable to use is not an easy task.

There are a few things you will have to do again and again:

- Write the HTML for the form itself
- Style the form in the right way
- Validate the data in the browser (optional)
- Validate the data on the server (never optional - don't forget!)
- Additionally make sure the user don't have to fight with the browser e.g. strange messages about "resending" data on reloading the page and using back- and forward-buttons.


Forget about fighting!
----------------------

Because we did not want to fight anymore, we are introducing today:

depage-forms – html5-forms made easy!


Documentation
Download [zip]
Fork us/Source-Code

depage-forms is PHP library for HTML form generation with focus on usability for developers and users.



It is part of the upcoming version of depage-cms, but it also works as a standalone library. By abstracting HTML, browser flaws (duplicate form submissions) and form validation, it provides a comfortable way to obtain reliable and validated data from users.



We are (by far) not the first trying to solve these problems. For PHP there are Zend-Forms and Phorms for example. But I think we found a rather unique approach to solve these problem.
So, how does it work?

First we load the Library and initialize an instance of the htmlforms-class. Almost every action goes through the htmlforms-class. Besides fieldsets and steps (later more about this) we only talk to instances of the htmlform directly.

```php
require_once('path/to/htmlform.php');
$form = new Depage\HtmlForm\HtmlForm('simpleForm');
```

After that, we add our inputs and other formfields to our form.

You can do this by calling the '"add" + element type' method. The first parameter is the name of the element. It has to be unique. The optional second parameter is an array of various element settings. Everything in the settings-array is optional and has a sensible default.

E.g.: If you don't give a label-parameter htmlforms will use the name of the input as label. You can also add a required-parameter, so you won't be able to finish the form without filling a required-input.

```php
$form->addText('username', [
    'label' => 'User name',
    'required' => true,
]);
$form->addEmail('email', [
    'label' => 'Email address',
]);
```

Next comes the processing — The process-method is essential and the backbone of the htmlforms-class:

- It validates submitted data if there is any.
- It redirects to the success page if all the data is valid and all required fields are filled in.
- It stores the data in the session and redirects to the form to circumvent the form resubmission problem. That means, that every form is sent with a POST-Request and then redirected to display the form again through a GET-Request. For more info: PRG-Pattern on Wikipedia

(Note: Make sure to call the process-method before any output, so the form is able to redirect itself.)

```php
$form->process();
```

Now after processing the form we know if the submitted data is valid:

- If the data is valid, we can do whatever we want with it. In this example we just dump it to the output. The submitted data is saved in a session until the session is cleared (which we do here) or the session has timed out.
- If the form is empty or the submitted data is not valid we output the form with "echo" which will output the html-source for the form.

```php
if ($form->validate()) {
    var_dump($form->getValues());
    $form->clearSession();
} else {
    echo ($form);
}
```

Here is the full example:
--------------------

@htmlonly<iframe class="example" seamless="seamless" src="../examples/simple.php"></iframe>@endhtmlonly

@include simple.php

Next Steps:
-----------

In the next posts about depage-forms we will introduce some extended features like:

- [form validation, the automatic type-casting of data-values and](Docs/Validation.md)
- subdividing forms into steps and step-navigation
- subclassing htmlform
