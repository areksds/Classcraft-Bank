<?php

require 'functions.php';
require 'dbconn.php';
require_once 'mailsender.php';
require 'config.php';

$now = time();

$db = new DbConn;
$stmt = $db->conn->prepare("SELECT * FROM activeLoans");
$stmt->setFetchMode(PDO::FETCH_OBJ);
if($stmt->execute() <> 0)
{

  while($loans = $stmt->fetch())
  {
      $seconddate = strtotime($loans->date);
      $datediff2 = $now - $seconddate;
      $datediff1 = floor($datediff2 / (60 * 60 * 24));
      $datediff = 14 - $datediff1;
      if ($datediff <= 0) {
      	if ($loans->emailed == 0) {
      		$email = userInfo($loans->username);
      		$m = new MailSender;
    		$m->sendMail($email['email'], $loans->username, $loans->amount, 'Overdue');
        $r = new MailSender;
    		$r->sendMail($teacher_email, $loans->username, $loans->amount, 'OverdueMention');

    		$status = 1;
    		$bdb = new DbConn;
		    $bstmt = $bdb->conn->prepare("UPDATE activeLoans SET emailed = :status WHERE username = :username");
		    $bstmt->bindParam(':status', $status);
		    $bstmt->bindParam(':username', $loans->username);
		    $bstmt->execute();
      	}
      }
      
  }

}



?>