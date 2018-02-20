<?php

session_start();
ob_start();

require 'includes/functions.php';

$amount = $_POST['amount'];
$reason = $_POST['reason'];
$response = loanRequest($_SESSION['username'], $amount, $reason);

$resp = new RespObj($_SESSION['username'], $response);
$jsonResp = json_encode($resp);
echo $jsonResp;

unset($resp, $jsonResp);


ob_end_flush();

?>