<?php
/**
 * Copyright (c) 2012 SOFORT AG
 *
 * $Date$
 * @version SofortLib 1.5.0rc  $Id$
 * @author SOFORT AG http://www.sofort.com (integration@sofort.com)
 *
 */
// read the notification from php://input  (http://php.net/manual/en/wrappers.php.php)
// this class should be used as a callback function
require_once('../../library/sofortLib.php');
$notification = new SofortLib_Notification();
$notification->getNotification();

echo $notification->getTime();
$transactionId = $notification->getTransactionId();

// fetch some information for the transaction id retrieved above
$transactionData = new SofortLib_TransactionData();
$transactionData->setTransaction($transactionId);
$transactionData->sendRequest();

echo '<table border="1">';
echo '<tr><td>transaction was: </td><td align="right">'.$transactionData->getTransaction().'</td></tr>';
echo '<tr><td>start date is: </td><td align="right">'.$transactionData->getSofortaboStartDate().'</td></tr>';
echo '<tr><td>amount is: </td><td align="right">'.$transactionData->getAmount().' '.$transactionData->getCurrency().'</td></tr>';
echo '<tr><td>interval is: </td><td align="right">'.$transactionData->getSofortaboInterval().'</td></tr>';
echo '<tr><td>minimum payments: </td><td align="right">'.$transactionData->getSofortaboMinimumPayments().'</td></tr>';
echo '<tr><td>status is: </td><td align="right">'.$transactionData->getStatus(). ' - '. $transactionData->getStatusReason().'</td></tr>';
echo '</table>';

