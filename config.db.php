<?php
define("DSN","mysql:host=localhost;dbname=hqpay");
define("USERNAME","root");
define("PASSWORD","ad3poju");

try{
	$pdo=new PDO(DSN,USERNAME,PASSWORD,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_ORACLE_NULLS=>PDO::NULL_EMPTY_STRING));
}
catch (PDOException $ex){
 	//log errors
}
