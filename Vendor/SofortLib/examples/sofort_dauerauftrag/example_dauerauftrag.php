<?php
/**
 * Copyright (c) 2012 SOFORT AG
 *
 * $Date: 2012-09-05 14:27:56 +0200 (Wed, 05 Sep 2012) $
 * @version SofortLib 1.5.0rc  $Id: example_dauerauftrag.php 5301 2012-09-05 12:27:56Z dehn $
 * @author SOFORT AG http://www.sofort.com (integration@sofort.com)
 *
 */

require_once('../../library/sofortLib.php');

define('CONFIGKEY', '11111:22222:1f8d368b22cb843e68950d8f5f1c13e7d'); //your configkey or userid:projektid:apikey

$Sofort = new SofortLib_Multipay(CONFIGKEY);
$Sofort->setSofortDauerauftrag();
$Sofort->setSofortDauerauftragInterval(1);
$Sofort->setSofortDauerauftragTotalPayments(10);
$Sofort->setSofortDauerauftragStartDate('2011-12-01');
$Sofort->setReason('Testzweck', 'Testzweck2');
$Sofort->setSenderAccount('88888888', '12345678', 'Max Mustermann');
$Sofort->setAmount(10);
$Sofort->setSuccessUrl('https://{website}/');
$Sofort->setAbortUrl('https://{website}/');
$Sofort->setTimeoutUrl('https://{website}/');
$Sofort->setNotificationUrl('https://{website}/');
$Sofort->sendRequest();

// user should be redirected to the generated Payment Url, where a wizard prompts the user for his banking credentials
// right after the user successfully proceeded through the wizard, a transaction id is being created and the user should be redirect back to the shop
// (redirection is being processed by Payment Network AG, URLs to be redirected to can be entered and altered in Payment Network AG Users Backend.)
if($Sofort->isError()) {
	echo $Sofort->getError();
} else {
	echo 'User should be redirected to: <a href="'.$Sofort->getPaymentUrl().'" target="_blank">'.$Sofort->getPaymentUrl().'</a>';
}

