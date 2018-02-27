<?php
//PUT THIS HEADER ON TOP OF EACH UNIQUE PAGE
session_start();
if (file_exists('../login/dbconf.php') == false || file_exists('../login/includes/dbconf.php') == false || file_exists('../login/config.php') == false || file_exists('../login/includes/config.php') == false) {
	header("location:installer/installer.php");
} elseif (!isset($_SESSION['username'])) {
    header("location:login/main_login.php");
} 

include "includes/functions.php";
include "includes/dbconn.php";
include "dbconf.php";
$balance = checkBalance($_SESSION['username']);  
$userInfo = userInfo($_SESSION['username']);
