<?php
include_once 'vendor/autoload.php';
define('PP_CONFIG_PATH',"vendor/paypal/sdk-core-php/tests");
include_once 'vendor/paypal/rest-api-sdk-php/sample/common.php';
include_once 'config.db.php';

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Address;
use PayPal\Api\CreditCard;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Payer;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;

class Gateway{
	
	function __construct(){
		global $pdo;
		$this->pdo=$pdo;
	}
	
	function paypal($data) {
		try {
			foreach ($data as $k=>$v) $$k=$v;
			include_once 'config.paypal.php';
			$apiContext = new ApiContext(new OAuthTokenCredential(CLIENT_ID,CLIENT_SECRET));
		
			list($m,$y)=explode("/", $card_expiry);
			$card = new CreditCard();
			$card->setNumber($card_number);
			$card->setType(strtolower($card_type));
			$card->setExpireMonth($m);
			$card->setExpireYear($y);
			$card->setCvv2($card_cvv);
			$card->setFirstName($first_name);
			$card->setLastName($last_name);
			
			$fi = new FundingInstrument();
			$fi->setCreditCard($card);
			
			$payer = new Payer();
			$payer->setPaymentMethod('credit_card');
			$payer->setFundingInstruments(array($fi));
			
			$amount = new Amount();
			$amount->setCurrency($currency);
			$amount->setTotal($price);
			
			$transaction = new Transaction();
			$transaction->setAmount($amount);
			$transaction->setDescription('Enter your card details and proceed');
			
			$payment = new Payment();
			$payment->setIntent('sale');
			$payment->setPayer($payer);
			$payment->setTransactions(array($transaction));
			
			$res = json_decode($payment->create($apiContext));
			
			$this->save($data,__FUNCTION__, $res, 1);
			return json_encode(["status"=>true,"msg"=>sprintf("Your payment has been %s",$res->state)]);
			
	} catch (Exception $e) {
		
		if ($e instanceof PPConfigurationException) {
		} 
		elseif ($e instanceof PPConnectionException) {
		}
		elseif ($e instanceof PayPal\Exception\PayPalConnectionException) {
			$res=json_decode($e->getData(),1);
			$this->save($data,__FUNCTION__, $res, 0);
			$msg=array_shift(isset($res["details"])?$res["details"]:[]);
			return json_encode(["status"=>false,"msg"=>($res["name"]=="UNKNOWN_ERROR" || empty($msg["issue"]))?("An unknown error has occurred"):(sprintf("%s %s",["cvv2"=>"CVV2","expire_year"=>"Card expiration","credit_card"=>"","type"=>"Invalid credit card number or","number"=>"Credit card number","expire_month"=>"Expiration month"][end(explode(".",$msg["field"]))],strtolower($msg["issue"])))]);
		}
		else {
			throw $e;
		}
		
		
	}
	}
	
	function braintree($data) {
		 	foreach ($data as $k=>$v) $$k=$v;
			try{
				include_once 'config.braintree.php';
				$customer = Braintree_Customer::create(['firstName' => $first_name,'lastName' => $last_name]);
				
				if(!isset($nonce) || empty($nonce)) Throw New Exception("An unknown error has occurred");
				
				if($customer->success){
					$transaction = Braintree_Transaction::sale([
							'amount' => $price,'customerId' => $customer->customer->id,
							'paymentMethodNonce' => $nonce
					]);
					
					if($transaction->success){
						$this->save($data,__FUNCTION__, $transaction->transaction, 1);
						return json_encode(["status"=>true,"msg"=>sprintf("Your payment has been %s",$transaction->transaction->status)]);
					}
					else{
						Throw New Exception($transaction->message);
					}
				}
			}
			catch (Exception $e){
				$this->save($data,__FUNCTION__, (string)$e, 0);
				return json_encode(["status"=>false,"msg"=>$e->getMessage()]);
			}
		
	}
	
	function save($order,$gateway,$response,$status) {
		$transaction=$this->pdo->prepare("insert into transactions set c_order=:order, gateway=:gateway, g_response=:response, status=:status,date=now()");
		$transaction->execute(["order"=>json_encode($order),"gateway"=>$gateway,"response"=>json_encode($response),"status"=>$status]);
			
	}
	
}