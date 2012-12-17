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

// given a transaction id, shop owner is able to fetch information about it using TransactionData class.

// request some information about transaction processed
$transactionData = new SofortLib_TransactionData(CONFIGKEY);	// connect to Payment Network with your given credentials
$transactionData->setTransaction('11111-22222-4E157628-5C27');	// set the above mentioned transaction ID
$transactionData->sendRequest();	// submit the request to Payment Network

// use the get methods to fetch information

$transactionData->getTransaction();
$transactionData->getSofortdauerauftragStartDate();
$transactionData->getAmount();
$transactionData->getCurrency();
$transactionData->getStatus();
$transactionData->getStatusReason();


// check, if payment ended with last received transaction
$lastPaymentRecieved = $transactionData->getSofortdauerauftragLastPaymentReceived();

// check the $j'th payment of the $i'th transaction (received|pending)
$statusOfPayment = $transactionData->getSofortdauerauftragStatusOfPayment();

// get the total sum paid via sofort Dauerauftrag
$sumPaid = $transactionData->getSofortdauerauftragPaymentsSum();

// get the status of all received payments
$allPaymentsReceived = $transactionData->getSofortdauerauftragAllPaymentsReceived();

