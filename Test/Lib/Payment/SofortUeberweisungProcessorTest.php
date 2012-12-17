<?php
App::uses('SofortUeberweisungProcessor', 'Sofort.Lib/Payment');
class SofortUeberweisungProcessorTest extends CakeTestCase {



	public function testPay() {
		$this->Sofort->pay();
	}

}