# Sofortüberweisung Payment Processor

Sofortueberweisung.de Payment Processor Plugin based on the standard payments lib

## Requirements

 * CakePHP 2.x http://github.com/cakephp/cakephp
 * Payments http://github.com/burzum/Payments

This plugin is based on the standard Payments lib, you'll need it.

## Setup ##

	git clone

### Configuration

	$config = array(
		'Sofort' => array(
		 	'apiKey' => 'YOUR-API-KEY'));

### Usage

The following fields are required by this processor to be set for the actions:

Read the Payments plugin readme.md how to set fields.

### Pay:

 * amount
 * payment_reason'

### Refund

 * sender_account_bic
 * sender_account_iban
 * sender_account_holder

## License ##

Copyright 2012, Florian Krämer

Licensed under [The LGPL License](http://www.opensource.org/licenses/lgpl-license.php)
