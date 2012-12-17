<?php
/**
 * Copyright (c) 2012 SOFORT AG
 *
 * $Date: 2012-09-05 14:27:56 +0200 (Wed, 05 Sep 2012) $
 * @version SofortLib 1.5.0  $Id: example_cancel_invoice.php 5301 2012-09-05 12:27:56Z dehn $
 * @author SOFORT AG http://www.sofort.com (integration@sofort.com)
 *
 */

require_once('../../../library/sofortLib.php');

define('CONFIGKEY', '1111:2222:9f5d237b65eb833e69520985f1c14e7c'); //your configkey or userid:projektid:apikey

$PnagInvoice = new PnagInvoice(CONFIGKEY);
$PnagInvoice->setVersion('MY_VERSION');
$PnagInvoice->addInvoiceAddress('success', 'Doe', 'Street', '15', '35578', 'City', 2, 'DE', 'Company Name');
$PnagInvoice->addShippingAddress('success', 'Doe', 'Street', '15', '35578', 'City', 2, 'DE', 'Company Name');
$PnagInvoice->setReason('Invoice', 'Invoice');
$PnagInvoice->setEmailCustomer('tester@example.com');
$PnagInvoice->addItemToInvoice(
	md5('unique'),					// unique term to represent each item
	'Art01', 						// article number, type number, ... defined in shop
	'a simple title', 				// title
	1.20, 							// unit price (incl. VAT)
	0, 								// product type
	'a simple description', 		// description
	6, 								// number of articles
	19								// VAT
);

$PnagInvoice->setAbortUrl('http://127.0.0.1');
$PnagInvoice->setSuccessUrl('http://127.0.0.1');
$PnagInvoice->setTimeoutUrl('http://127.0.0.1');
$PnagInvoice->setNotificationUrl('http://127.0.0.1');

try {
	$err = $PnagInvoice->checkout();
	getWebPage($PnagInvoice->getPaymentUrl());
} catch (XmlToArrayException $e) {

}

$transactionId = $PnagInvoice->getTransactionId();

$PnagInvoice = new PnagInvoice(CONFIGKEY, $transactionId);
$creditNoteNumber = '13214';
$PnagInvoice->cancelInvoice($transactionId, $creditNoteNumber);

/**
 * Get a web file (HTML, XHTML, XML, image, etc.) from a URL.  Return an
 * array containing the HTTP server response header fields and content.
 */
function getWebPage($url) {
	$options = array(
	CURLOPT_RETURNTRANSFER => true,     // return web page
	CURLOPT_HEADER         => false,    // don't return headers
	CURLOPT_FOLLOWLOCATION => true,     // follow redirects
	CURLOPT_ENCODING       => "",       // handle all encodings
	CURLOPT_USERAGENT      => "sofort example", // who am i
	CURLOPT_AUTOREFERER    => true,     // set referer on redirect
	CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
	CURLOPT_TIMEOUT        => 120,      // timeout on response
	CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
	CURLOPT_SSL_VERIFYPEER => false,
	);

	$ch = curl_init($url);
	curl_setopt_array($ch, $options);
	$content = curl_exec($ch);
	$err = curl_errno($ch);
	$errmsg = curl_error($ch);
	$header = curl_getinfo($ch);
	curl_close($ch);

	$header['errno'] = $err;
	$header['errmsg'] = $errmsg;
	$header['content'] = $content;
	return $header;
}

echo $PnagInvoice->getTransactionId();
?>