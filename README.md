vies-vat-validator
==================

Validates an Euopean VAT code against European Comission VIES Database

http://ec.europa.eu/taxation_customs/vies/faqvies.do#item_16

It's inspired by: http://isvat.appspot.com/

Examples
-------------

`php vies.php ES B63920920`

country: A valid European ISO country code.

number: Vat number to request.

Response
-------

In case of successful interrogation, the response is either `Code is valid` or `Code is not valid`


Error logging
--------------

There is a minimal error reporting. 

The errors shown are:

- Invalid country
- No vat / country specified.
