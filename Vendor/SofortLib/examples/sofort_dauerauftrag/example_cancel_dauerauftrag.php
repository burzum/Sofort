<?php
/**
 * Copyright (c) 2012 SOFORT AG
 *
 * $Date: 2011-07-07 14:09:35 +0200 (Do, 07. Jul 2011) $
 * @version SofortLib 1.5.0rc  $Id: example_dauerauftrag.php 1131 2011-07-07 12:09:35Z dehn $
 * @author SOFORT AG http://www.sofort.com (integration@sofort.com)
 *
 */

require_once('../../library/sofortLib.php');

define('CONFIGKEY', '11111:22222:1f8d368b22cb843e68950d8f5f1c13e7d'); //your configkey or userid:projektid:apikey

// given a transaction id, shop owner is able to handle a sofort Dauerauftrag
$transactionId = '11111-22222-4E157628-5C27';

$sofort = new SofortLib_CancelSa(CONFIGKEY);
$sofort->removeSofortDauerauftrag($transactionId);

$sofort->sendRequest();	// submit the request to Payment Network

$url = $sofort->getCancelUrl();	// redirect the customer to this URL to confirm the cancelation