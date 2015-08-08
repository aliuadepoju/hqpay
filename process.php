<?php
ini_set("display_errors",1);

if(isset($_GET["data"]) && !empty($_GET["data"])){
	$data=json_decode($_GET["data"],1);
	$splitCustomerName=explode(" ",$data["customer_name"]);
	$data["first_name"]=array_shift($splitCustomerName);
	$data["last_name"]=end($splitCustomerName);
	
	/* 
	print("<pre>");
	print_r($data);
	print("</pre>");
	exit;
	 */
	include_once 'Gateway.php';
	switch ($data["gateway"]) {
		case "paypal":
		$res=(new Gateway)->paypal($data);
		break;
		
		case "braintree":
			$res=(new Gateway)->braintree($data);
		break;
		
		default:
		break;
	}
	echo "<script>window.parent.Pay.callback('{$res}');</script>";
}




