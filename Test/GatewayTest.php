<?php
include_once 'Gateway.php';
/**
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
Class GatewayTest extends PHPUnit_Framework_TestCase{
	private $gateway;
	
	protected function setup(){
		$this->gateway=new Gateway();
	}
	
	protected function tearDown(){
		$this->gateway=NULL;
	}
	
	public function testPaypal(){
	    $result=$this->gateway->paypal(["customer_name"=>"Bisola Adepoju","price"=>"12000","currency"=>"USD","first_name"=>"Aliu","last_name"=>"Adepoju","card_number"=>"5399831626031104","card_expiry"=>"02/2020","card_cvv"=>"123","card_type"=>"Mastercard","gateway"=>"paypal"]);
	    $this->assertJson($result);
	}
	
	public function testBraintree(){
	    $result=$this->gateway->braintree(["customer_name"=>"Bisola Adepoju","price"=>"12000","currency"=>"USD","first_name"=>"Aliu","last_name"=>"Adepoju","card_number"=>"5399831626031104","card_expiry"=>"02/2020","card_cvv"=>"123","card_type"=>"Mastercard","gateway"=>"braintree"]);
	    $this->assertJson($result);
	}
}