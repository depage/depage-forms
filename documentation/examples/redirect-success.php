<?php
/*
 * Redirect to success page example
 *
 * Once the form is validated, it can also be redirected to another URL. This
 * can be done by setting the 'successURL' parameter in the form.
 */

/*
 * Load the library...
 */
require_once('../../htmlform.php');

/*
 * Create the example form 'originForm' with different success page url.
 */
$form = new depage\htmlform\htmlform('originForm');

/*
 * Do something useful with the data and clear the session.
 */
echo('<a href="">redirect.php</a>');
echo('<pre>');
var_dump($form->getValues());
echo('</pre>');

$form->clearSession();
