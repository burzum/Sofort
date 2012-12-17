<?php
/**
 * Copyright (c) 2012 SOFORT AG
 *
 * $Date: 2012-09-05 14:27:56 +0200 (Wed, 05 Sep 2012) $
 * @version SofortLib 1.5.0rc  $Id: example_transactionData.php 5301 2012-09-05 12:27:56Z dehn $
 * @author SOFORT AG http://www.sofort.com (integration@sofort.com)
 *
 */

require_once('../../library/sofortLib.php');

define('CONFIGKEY', '12345:67890:123456789abcdef123456abcdef12345'); //your configkey or userid:projektid:apikey


$sofort = new SofortLib_TransactionData(CONFIGKEY);
// use an array of transactionIDs (or set a single transactionID if you like, just pass a string to setTransaction)
$transactionIds[] = '16263-99178-4E019D4F-5E12';
$transactionIds[] = '16263-99178-4E01B143-5E25';
$sofort->setTransaction($transactionIds);

// or simply add a single transactionID, using addTransaction (you could easily add an array of transactionIDs as well!)
$singleTransactionId = '16263-99178-4E01B5E0-1ACD';
$sofort->addTransaction($singleTransactionId);

$sofort->sendRequest();

// get the information you need, depends on what transaction you where looking for...
echo 'Amount: '.$sofort->getAmount(0)."<br />";
echo 'Status: '.$sofort->getStatus(0);