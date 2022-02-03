<?php

include_once('arbpg.php');


$ARB_MERCHANT_HOSTED_ENDPOINT_TESTING = 'https://securepayments.alrajhibank.com.sa/pg/payment/tranportal.htm';
$ARB_PAYMENT_ENDPOINT_TESTING = 'https://securepayments.alrajhibank.com.sa/pg/paymentpage.htm?PaymentID=';

$ARB_MERCHANT_HOSTED_ENDPOINT_PRODUCTION = 'https://digitalpayments.alrajhibank.com.sa/pg/payment/hosted.htm';
$ARB_PAYMENT_ENDPOINT_PRODUCTION = 'https://digitalpayments.alrajhibank.com.sa/pg/paymentpage.htm?PaymentID=';


$card_number= $_POST['card_number'];

$expiry_month= $_POST['expiry_month'];
$expiry_year= $_POST['expiry_year'];
$cvv= $_POST['cvv'];
$card_holder = $_POST['holder_name'];


$arbPg = new ArbPg();

$arbPg->test();

$url = $arbPg->getmerchanthostedPaymentid($card_number, $expiry_month, $expiry_year, $cvv, $card_holder);


echo $url;
// $url= $ARB_PAYMENT_ENDPOINT_TESTING . $paymentId; //in Production use Production End Point


 header("Location: $url");


