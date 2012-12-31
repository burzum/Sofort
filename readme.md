# Sofortüberweisung Payment Processor

Sofortueberweisung.de Payment Processor Plugin based on the standard payments lib

## Requirements

 * CakePHP 2.x http://github.com/cakephp/cakephp
 * Payments http://github.com/burzum/Payments

This plugin is based on the standard Payments lib, you'll need it.

## Setup ##

Clone from github

	git clone git://github.com/burzum/Sofort.git

## Usage

Configure a live API connection

	$Config = new PaymentProcessorConfig(array(
		'apiKey' => 'YOU-SOFORT-API-KEY');
	$Processor = new SofortUeberweisungProcessor($Config, 'default');

Configure sandbox API connection

	$Config = new PaymentProcessorConfig(array(
		'apiKey' => 'YOU-SOFORT-API-KEY');
	$Processor = new SofortUeberweisungProcessor($Config, 'default', true);

## Processing payment actions

### Pay

Payments require a primary reason to be set and an secondary optionally set

	$Processor->set('payment_reason', 'Order 123'); // required
	$Processor->set('payment_reason2', 'Something here'); // optional
	$Processor->pay(15.99);

### Refund

Sofort Refund requires you to set a bank account, usually the one that received the money before, to send the money from back to the buyer.

	$Processor->set('sender_account_bic', 'Order 123'); // required
	$Processor->set('sender_account_iban', 'Something here'); // required
	$Processor->set('sender_account_holder', 'Something here'); // required
	$Processor->refund(null, 15.99, 'My comment');

## License ##

Copyright 2012, Florian Krämer

Licensed under [The LGPL License](http://www.opensource.org/licenses/lgpl-license.php)
