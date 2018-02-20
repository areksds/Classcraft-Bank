<?php

session_start();
ob_start();

require 'includes/functions.php';

$email = $_POST['email'];
$amount = $_POST['amount'];
$from = $_POST['from'];
$username = userInfoEmail($email);

if ($from != $_SESSION['username']){

$resp = new RespObj($username['username'], "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Username authentication error. Please don't mess with inspect element.</div>");
$jsonResp = json_encode($resp);
echo $jsonResp;

unset($resp, $jsonResp);

} else {

$response = sendMoney($from, $email, $amount);

$resp = new RespObj($username['username'], $response);
$jsonResp = json_encode($resp);
echo $jsonResp;

unset($resp, $jsonResp);

}

ob_end_flush();

?>