<?php
/**
 * Copyright (c) 2012 SOFORT AG
 *
 * $Date: 2012-09-05 14:27:56 +0200 (Wed, 05 Sep 2012) $
 * @version SofortLib 1.5.0rc  $Id: example_sofortlastschrift.php 5301 2012-09-05 12:27:56Z dehn $
 * @author SOFORT AG http://www.sofort.com (integration@sofort.com)
 *
 */

require_once('../../library/sofortLib.php');

define('CONFIGKEY', '1111:2222:3f6a263f65eb633e67530885b1a12e7c'); //your configkey or userid:projektid:apikey

$Sofort = new SofortLib_Multipay(CONFIGKEY);
$Sofort->setSofortlastschrift();
$Sofort->setReason('Testzweck', 'Testzweck2');
$Sofort->setSenderAccount('88888888', '12345678', 'Max Mustermann');
$Sofort->setAmount(10);
$Sofort->setSuccessUrl('https://{website}/');
$Sofort->setAbortUrl('https://{website}/');
$Sofort->setTimeoutUrl('https://{website}/');
$Sofort->setNotificationUrl('https://{website}/');
$Sofort->sendRequest();

if($Sofort->isError()) {
	//PNAG-API didn't accept the data
	echo $Sofort->getError();
} else {
	//buyer must be redirected to $paymentUrl else payment cannot be successfully completed!
	$paymentUrl = $Sofort->getPaymentUrl();
	header('Location: '.$paymentUrl);
}