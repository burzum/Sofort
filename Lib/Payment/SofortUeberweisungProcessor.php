<?php
App::uses('BasePaymentProcessor', 'Cart.Lib/Payment');
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
 * Constructor
 *
 * @param array $options
 * @return \SofortUeberweisungProcessor
 */
	public function __construct($options = array()) {
		parent::__construct($options);

		$this->configKey = Configure::read('Sofort.configKey');
	}

/**
 *
 */
	protected function _getApiInstance() {
		$this->Multipay = new SofortLib_Multipay($this->configKey);
		$this->Multipay->setNotificationUrl(Router::url($this->callbackUrl, true));
		$this->Multipay->setAbortUrl(Router::url($this->cancelUrl, true));
		$this->Multipay->setSuccessUrl(Router::url($this->finishUrl, true));
	}

/**
 *
 */
	public function pay($order) {
		$this->_ueberweisung($order);
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
 * @param array $order
 * @return boolean
 */
	public function parseNotification($order) {
		$sofort = new SofortLib_Notification();

		if ($sofort->isError()) {
			$this->log($sofort->getErrors(), 'payment-error');
			return false;
		}

		if ($sofort->isWarning()) {
			$this->log($sofort->getWarnings(), 'payment-warning');
		}

		$transactionId = $sofort->getNotification();
		$this->log($transactionId, 'payment-debug');

		$sofort = new SofortLib_TransactionData($this->configKey);
		$sofort->setTransaction($transactionId)->sendRequest();

		$status = $sofort->getStatus();

		if ($status == 'pending') {
			$order[$this->OrderModel->alias]['payment_status'] = 'pending';
			$order[$this->OrderModel->alias]['status'] = 'complete';
		}

		if ($status == 'received') {
			$order[$this->OrderModel->alias]['payment_status'] = 'received';
		}

		return $this->OrderModel->save($order);
	}

/**
 *
 */
	protected function _ueberweisung($order) {
		$this->_getApiInstance();

		$this->Multipay->setSofortueberweisung();
		$this->Multipay->setAmount($order['Order']['total']);
		$this->Multipay->setReason($order['Order']['id'], $order['Order']['user_id']);
		$this->Multipay->sendRequest();

		if ($this->Multipay->isError()) {
			$this->log($this->Multipay->getErrors(), 'payment-error');
			throw new PaymentApiException(__d('sofort', 'An error occurred please contact the shop owner.'));
		}

		if ($this->Multipay->isWarning()) {
			$this->log($this->Multipay->getWarnings(), 'payment-warning');
		}

		$this->redirect($this->Multipay->getPaymentUrl());
	}

/**
 * Refund money
 *
 * @param float $amount
 * @param string $comment
 * @param array $order
 * @return mixed
 * @todo finish me
 */
	public function refund($amount, $comment, $order) {
		$sofort = new SofortLib_Refund($this->configKey);
		$sofort->addRefund($order['Order']['payment_reference'], $amount, $comment);
		$sofort->setSenderAccount('12345678', '12345678', 'Iam Holder of this Account');
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
			$status = 'failed';
		}

		if ($status == 'loss' && $reason == 'not_credited') {
			$message = __d('sofort', 'Das Geld ist nicht eingegangen..');
			$status = 'failed';
		}

		if ($status == 'pending' && $reason == 'not_credited_yet') {
			$message = __d('sofort', 'Das Geld ist noch nicht eingegangen..');
			$status = 'pending';
		}

		if ($status == 'received' && $reason == 'consumer_protection') {
			$message = __d('sofort', 'Das Geld ist auf dem Treuhandkonto eingegangen.');
			$status = 'finished';
		}

		if ($status == 'received' && $reason == 'credited') {
			$message = __d('sofort', 'Das Geld ist eingegangen.');
			$status = 'finished';
		}

		if ($status == 'refunded' && $reason == 'compensation') {
			$message = __d('sofort', 'Das Geld wurde zurückerstattet (Teilrückbuchung).');
			$status = 'refunded';
		}

		if ($status == 'refunded' && $reason == 'refunded') {
			$message = __d('sofort', 'Das Geld wurde zurückerstattet (komplette Rückbuchung des Gesamtbetrags).');
			$status = 'refunded';
		}

		return compact($status, $message);
	}

}