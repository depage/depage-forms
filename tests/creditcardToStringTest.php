<?php

use depage\htmlform\elements\creditcard;

/**
 * Tests for creditcard element rendering.
 **/
class creditcardToStringTest extends PHPUnit_Framework_TestCase {
    // {{{ testSimple()
    /**
     * Element with default setup
     **/
    public function testSimple() {
        $form = new nameTestForm();

        $creditcard = new creditcard('creditcardName', array(), $form);
        $creditcard->addChildElements();

        $expected = '<fieldset id="formName-creditcardName" name="creditcardName">' .
            '<legend>creditcardName</legend>' .
            '<p id="formName-creditcardName_card_type" class="input-single" data-errorMessage="Please enter valid data!">' .
                '<label>' .
                    '<span class="label"></span>' .
                    '<select name="creditcardName_card_type">' .
                        '<option value="visa">Visa</option>' .
                        '<option value="americanexpress">American Express</option>' .
                        '<option value="mastercard">MasterCard</option>' .
                    '</select>' .
                '</label>' .
            '</p>' . "\n" .
            '<p id="formName-creditcardName_card_number" class="input-text required" data-errorMessage="Please enter valid data!">' .
                '<label>' .
                    '<span class="label">Creditcard Number <em>*</em></span>' .
                    '<input name="creditcardName_card_number" type="text" required pattern="^(?:\d[ -]*?){13,16}$" value="">' .
                '</label>' .
            '</p>' . "\n" .
            '<p id="formName-creditcardName_card_numbercheck" class="input-text required" data-errorMessage="Please enter valid data!">' .
                '<label>' .
                    '<span class="label">CVV/CVC <em>*</em></span>' .
                    '<input name="creditcardName_card_numbercheck" type="text" required pattern="^\d{3,4}$" value="">' .
                '</label>' .
            '</p>' . "\n" .
            '<p id="formName-creditcardName_card_expirydate" class="input-text required" data-errorMessage="Please enter valid data!">' .
                '<label>' .
                    '<span class="label">Expiration Date MM/YY <em>*</em></span>' .
                    '<input name="creditcardName_card_expirydate" type="text" required pattern="^\d{2}\/\d{2}$" value="">' .
                '</label>' .
            '</p>' . "\n" .
            '<p id="formName-creditcardName_card_owner" class="input-text required" data-errorMessage="Please enter valid data!">' .
                '<label>' .
                    '<span class="label">Card Owner <em>*</em></span>' .
                    '<input name="creditcardName_card_owner" type="text" required value="">' .
                '</label>' .
            '</p>' . "\n" .
        '</fieldset>' . "\n";

        $this->assertEquals($expected, $creditcard->__toString());
    }
    // }}}
}
