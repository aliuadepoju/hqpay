<?php
ini_set("display_errors",1);
include_once 'vendor/autoload.php';

try {
	include_once 'config.braintree.php';
	$clientToken = Braintree_ClientToken::generate();
} catch (Exception $e) {
	
}
?>
<!DOCTYPE html>
<html>
<head>
   <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Payment Library</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
      <link href="assets/css/bootstrap.css" rel="stylesheet">
<style type="text/css">
	.hide2{
	display: none;
	}
	
	.rel{
	position: relative;
	}
	
	.disable{
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background: #fff;
	cursor: not-allowed;
	opacity: 0.7;
	filter: alpha(opacity=70);
	text-align: center;
	z-index: 10;
}
</style>
  </head>
  <body>

<div class="container">
<div class="row">
<div class="clearfix">&nbsp;</div>
<div class="clearfix">&nbsp;</div>
<div class="clearfix">&nbsp;</div>
<div class="col-md-4"></div>
<div class="col-md-4 rel">
<div class="disable hide2"></div>
<div id="err"></div>
<form action="javascript:void(0)" method="post">
  
  <div class="panel panel-default">
  
  <div class="panel-heading">
  Order
  </div>
  <div class="panel-body">
  
  <div class="form-group">
    <label>Price</label>
   <div class="input-group">
   <input class="form-control" type="text" data-hq-name="price" placeholder="0.00">
   <div class="input-group-addon">
   <select data-hq-name="currency">
   <option selected disabled>Currency</option>
  <option value="USD">USD</option>
   <option value="THB">THB</option>
   <option value="HKD">HKD</option>
   <option value="SGD">SGD</option>
   <option value="AUD">AUD</option>
   </select>
   </div>
    </div>
   
  </div>
  
  <div class="form-group">
    <label>Customer name</label>
    <input type="text" class="form-control" data-hq-name="customer_name" placeholder="Firstname Lastname">
  </div>
  
  </div>
  
  <div class="panel-heading">
  Payment
  </div>
  <div class="panel-body">
  <div class="form-group">
    <label>Holder name</label>
    <input type="text" data-hq-name="cardholder_name" class="form-control" placeholder="Firstname Lastname">
  </div>
  
  <div class="form-group">
   <div class="row">
   <div class="col-xs-9">
    <label>Card number</label>
    <input type="text" data-hq-name="card_number" class="form-control">
 
   </div>
   <div class="col-xs-3">
    <label>CVV</label>
    <input type="text" data-hq-name="card_cvv" class="form-control">
 
   </div>
   </div>
   
    </div>
  
   <div class="form-group">
    <label>Expiration</label>
    <input type="text" data-hq-name="card_expiry" class="form-control" placeholder="MM/YYYY">
  </div>
  
     
  </div>
  
  </div>
  <iframe src="javascript:void(0)" name="payIframe" id="pay_iframe" class="hide"></iframe>
  <button type="submit" id="submit" onclick="Pay.submit()" class="btn btn-primary btn-block">Pay</button>
</form>
</div>
<div class="col-md-4"></div>
</div>
</div>
  
<script type="text/javascript" charset="utf8" src="assets/js/jquery-2.1.1.min.js"></script>
<script src="https://js.braintreegateway.com/v2/braintree.js"></script>
<?php include_once 'js.php';?>
</body>
</html>