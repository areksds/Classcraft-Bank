<?php

session_start();
ob_start();

require 'includes/functions.php';

$response = loanpay($_SESSION['username']);

$resp = new RespObj($_SESSION['username'], $response);
$jsonResp = json_encode($resp);
echo $jsonResp;

unset($resp, $jsonResp);


ob_end_flush();

?>