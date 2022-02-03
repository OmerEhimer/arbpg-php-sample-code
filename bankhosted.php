<?php
include_once('arbpg.php');

$ARB_HOSTED_ENDPOINT_TESTING = 'https://digitalpayments.alrajhibank.com.sa/pg/payment/hosted.htm';
$ARB_PAYMENT_ENDPOINT_TESTING = 'https://digitalpayments.alrajhibank.com.sa/pg/paymentpage.htm?PaymentID=';

$ARB_HOSTED_ENDPOINT_PRODUCTION = 'https://digitalpayments.alrajhibank.com.sa/pg/payment/tranportal.htm';
$ARB_PAYMENT_ENDPOINT_PRODUCTION = 'https://digitalpayments.alrajhibank.com.sa/pg/paymentpage.htm?PaymentID=';
echo " ARB PG Payment";

$arbPg = new ArbPg();

$arbPg->test();

$paymentId = $arbPg->getPaymentId();


$url= $ARB_PAYMENT_ENDPOINT_TESTING . $paymentId; //in Production use Production End Point

var_dump($url);
die();
header("Location: $url");