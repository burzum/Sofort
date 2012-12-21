<?php
App::uses('BasePaymentProcessor', 'Payments.Lib/Payment');
require_once(CakePlugin::path('Sofort') . 'Vendor' . DS . 'SofortLib' . DS . 'library' . DS . 'sofortLib.php');

/**
 * Sofortüberweisung Payment Processor
 *
 * @author Florian Krämer
 * @copyright Florian Krämer 2012
 * @license LGPL v3
 */
class SofortUeberweisungProcessor extends BasePaymentProcessor {

/**
 * SofortLib_Multipay instance
 *
 * @var SofortLib_Multipay
 */
	public $MultiPay = null;

/**
 * Values to be used by the API implementation
 *
 * Structure of the array is:
 * MethodName/VariableName/OptionsArray
 *
 * @var array
 */
	protected $_fields = array(
		'pay' => array(
			'amount' => array(
				'required' => true,
				'type' => array('integer', 'float', 'string')),
			'payment_reason' => array(
				'required' => true,
				'type' => array('string')
			),
		),
	);

/**
 * Constructor
 *
 * @param array $options
 * @return SofortUeberweisungProcessor
 */
	public function __construct($options = array()) {
		parent::__construct($options);

		$this->configKey = Configure::read('Sofort.configKey');
	}

/**
 * Configures the Multipay instance with the correct callback urls and makes it
 * available as $this->Multipay
 *
 * @return void
 */
	protected function _getMultipayInstance() {
		$this->Multipay = new SofortLib_Multipay($this->configKey);
		$this->Multipay->setNotificationUrl($this->callbackUrl);
		$this->Multipay->setAbortUrl($this->cancelUrl);
		$this->Multipay->setSuccessUrl($this->finishUrl);
	}

/**
 * Method to initialize the payment
 *
 * @param float $amount
 * @param array $options
 * @return void
 */
	public function pay($amount, array $options = array()) {
		$this->set('amount', (float) $amount);
		$this->validateFields('pay');

		$this->_ueberweisung($amount);
	}

/**
 *
 */
	public function confirmOrder() {
		$this->log($this->_request, 'sofort-confirmOrder');
		return true;
	}

/**
 * Notification callback
 *
 * @param array $options
 * @return boolean
 */
	public function notificationCallback(array $options) {
		$sofort = new SofortLib_Notification();

		if ($sofort->isError()) {
			$this->log($sofort->getErrors(), PaymentApILog::ERROR);
			return false;
		}

		if ($sofort->isWarning()) {
			$this->log($sofort->getWarnings(), PaymentApILog::WARNING);
		}

		$transactionId = $sofort->getNotification();
		$this->log($transactionId, PaymentApILog::DEBUG);

		$sofort = new SofortLib_TransactionData($this->configKey);
		$sofort->setTransaction($transactionId)->sendRequest();

		$status = $sofort->getStatus();

		if ($status == 'pending') {
			return PaymentStatus::PENDING;
		}

		if ($status == 'received') {
			return PaymentStatus::SUCCESS;
		}
	}

/**
 * Cancels a payment
 *
 * @param string $paymentReference
 * @param array $options
 * @return mixed
 */
	public function cancel($paymentReference, array $options) {

	}

/**
 *
 */
	protected function _ueberweisung() {
		$this->_getMultipayInstance();

		$this->Multipay->setSofortueberweisung();
		$this->Multipay->setAmount($this->field('amount'));
		$this->Multipay->setReason($this->field('payment_reason'), $this->field('payment_reason2'));
		$this->Multipay->sendRequest();

		if ($this->Multipay->isError()) {
			$this->log($this->Multipay->getErrors(), PaymentApiLog::ERROR);
			throw new PaymentApiException(__d('sofort', 'An error occurred please contact the shop owner.'));
		}

		if ($this->Multipay->isWarning()) {
			$this->log($this->Multipay->getWarnings(), PaymentApiLog::WARNING);
		}

		$this->redirect($this->Multipay->getPaymentUrl());
	}

/**
 * Refund money
 *
 * @param $paymentReference
 * @param float $amount
 * @param string $comment
 * @param array $options
 * @return mixed
 */
	public function refund($paymentReference, $amount, $comment = '', array $options) {
		$this->set('amount', $amount);
		$this->set('comment', $comment);
		$this->set('payment_reference', $paymentReference);
		$this->validateFields();

		$sofort = new SofortLib_Refund($this->configKey);

		$sofort->addRefund(
			$this->_fields['payment_reference'],
			$this->_fields['amount'],
			$this->_fields['comment']);

		$sofort->setSenderAccount(
			$this->_fields['sender_account_bic'],
			$this->_fields['sender_account_iban'],
			$this->_fields['sender_account_holder']);

		$sofort->sendRequest();
	}

/**
 * Scnittestellenbeschreibung_SOFORT_Überweisung.pdf Page 26
 *
 * @param $status
 * @param $reason
 * @return array
 */
	public function status($status, $reason) {
		if ($status == 'loss' && $reason == 'complaint') {
			$message = __d('sofort', 'Der Käuferschutz wurde in Anspruch genommen.');
			$status = PaymentStatus::FAILED;
		}

		if ($status == 'loss' && $reason == 'not_credited') {
			$message = __d('sofort', 'Das Geld ist nicht eingegangen..');
			$status = PaymentStatus::FAILED;
		}

		if ($status == 'pending' && $reason == 'not_credited_yet') {
			$message = __d('sofort', 'Das Geld ist noch nicht eingegangen..');
			$status = PaymentStatus::PENDING;
		}

		if ($status == 'received' && $reason == 'consumer_protection') {
			$message = __d('sofort', 'Das Geld ist auf dem Treuhandkonto eingegangen.');
			$status = PaymentStatus::SUCCESS;
		}

		if ($status == 'received' && $reason == 'credited') {
			$message = __d('sofort', 'Das Geld ist eingegangen.');
			$status = PaymentStatus::SUCCESS;
		}

		if ($status == 'refunded' && $reason == 'compensation') {
			$message = __d('sofort', 'Das Geld wurde zurückerstattet (Teilrückbuchung).');
			$status = PaymentStatus::PARTIAL_REFUNDED;
		}

		if ($status == 'refunded' && $reason == 'refunded') {
			$message = __d('sofort', 'Das Geld wurde zurückerstattet (komplette Rückbuchung des Gesamtbetrags).');
			$status = PaymentStatus::REFUNDED;
		}

		return compact($status, $message);
	}

}