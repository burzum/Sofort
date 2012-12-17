<?php
/**
 * Copyright (c) 2012 SOFORT AG
 *
 * $Date: 2011-07-07 11:29:07 +0200 (Do, 07. Jul 2011) $
 * @version SofortLib 1.5.0rc  $Id: example_transactionData.php 1125 2011-07-07 09:29:07Z dehn $
 * @author SOFORT AG http://www.sofort.com (integration@sofort.com)
 *
 */

require_once('../../library/sofortLib.php');

define('CONFIGKEY', '12345:67890:123456789abcdef123456abcdef12345'); //your configkey or userid:projektid:apikey

$sofort = new SofortLib_Refund(CONFIGKEY);
// add the amount of money you would like to be refunded... provide a comment also
$sofort->addRefund($transactionId, 10, 'enter a comment here');
$sofort->setSenderAccount('12345678', '12345678', 'Iam Holder of this Account');
$sofort->sendRequest();