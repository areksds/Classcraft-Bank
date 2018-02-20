<?php

session_start();
ob_start();
require 'includes/functions.php';

$amount = $_POST['amount'];
$username = $_POST['username'];

if ($username != $_SESSION['username']){

$resp = new RespObj($username['username'], "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Username authentication error. Please don't mess with inspect element.</div>");
$jsonResp = json_encode($resp);
echo $jsonResp;

unset($resp, $jsonResp);

} else {

$response = withdrawrequest($username, $amount);

$resp = new RespObj($username, $response);
$jsonResp = json_encode($resp);
echo $jsonResp;

unset($resp, $jsonResp);

}

ob_end_flush();

?>