<!DOCTYPE html>
<html>
  <head>
    <link href="../../css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="../../css/main.css" rel="stylesheet" media="screen">
    <meta charset="UTF-8">
    <title>Deposit Request</title>
  </head>
  <body>
<?php
require 'functions.php';
require 'dbconn.php';
require 'mailsender.php';
include '../config.php';

//Pulls variables from url. 
$verification = $code;
$code = $_GET['code'];
$status = $_GET['status'];
$amount = $_GET['amount'];
$username = $_GET['username'];
$email = userInfo($username);
$balance = checkBalance($username);
$bank = checkBalance($bankusername);
$clearPending = 0;
$finalamount = $balance['userBalance'] + $amount;
$bankamount = $bank['userBalance'] + $amount;

if ($code != $verification) {
    echo 'Request authentication error. Please contact an administrator for more information.';
} elseif ($balance['pendingIn'] <= 0) {
    echo 'Request invalid.';
} elseif ($status == 0) { 
    
    if(isset($_POST['submit'])){ 
        $input = $_POST['reason'];
        if ($input == ""){
             $message = "<p style=\"color: red; text-align: center;\">Please enter a reason.</p>";
        } else {
            $message = "<p style=\"color: green; text-align: center;\">Response recorded. You may now close this page.</p>";

            $bdb = new DbConn;
            $bstmt = $bdb->conn->prepare("UPDATE balance SET pendingIn = :pending WHERE username = :username");
            $bstmt->bindParam(':pending', $clearPending);
            $bstmt->bindParam(':username', $username);
            $bstmt->execute();

            $m = new MailSender;
            $m->sendReasonMail($email['email'], $username, $amount, $input, 'DepositReject');

            logAction("DEPOSIT REJECT", $username, "Deposit request for ".$amount." rejected for reason: ".$input);
        }
        
    } ?>

    <?php if (isset($_POST['submit'])){ if ($message == "<p style=\"color: red; text-align: center;\">Please enter a reason.</p>") { ?> 
    <form style="text-align: center;" method="post" action="">
    <h3>Why are you rejecting this request?</h3>
    <br>
    Reason: <input type="text" name="reason" id="reason" placeholder="I don't feel like it"></input>
    <br> <br>
    <button name="submit" id="submit" type="submit" class="btn btn-primary">Submit reason</button>
    <br> <br>
    <div><?php echo $message; ?></div>
    </form>
    <?php } elseif ($message == "<p style=\"color: green; text-align: center;\">Response recorded. You may now close this page.</p>") { echo $message; }} else { ?>
    <form style="text-align: center;" method="post" action="">
    <h3>Why are you rejecting this request?</h3>
    <br>
    Reason: <input type="text" name="reason" id="reason" placeholder="Not enough in balance"></input>
    <br> <br>
    <button name="submit" id="submit" type="submit" class="btn btn-primary">Submit reason</button>
    </form>
    <?php } ?>
    
<?php } elseif ($status == 1) { ?>

    <div style="text-align: center;">
    <h3>Request Accepted</h3>
    <br>
    <p>You may now close this window.</p>
    <br>
    </div>

<?php 
    
    $bdb = new DbConn;
    $bstmt = $bdb->conn->prepare("UPDATE balance SET pendingIn = :pending WHERE username = :username");
    $bstmt->bindParam(':pending', $clearPending);
    $bstmt->bindParam(':username', $username);
    $bstmt->execute();

    $vdb = new DbConn;
    $vstmt = $vdb->conn->prepare("UPDATE balance SET userBalance = :balance WHERE username = :username");
    $vstmt->bindParam(':balance', $finalamount);
    $vstmt->bindParam(':username', $username);
    $vstmt->execute();

    $sdb = new DbConn;
    $sstmt = $sdb->conn->prepare("UPDATE balance SET userBalance = :balance WHERE username = :username");
    $sstmt->bindParam(':balance', $bankamount);
    $sstmt->bindParam(':username', $bankusername);
    $sstmt->execute();

    $m = new MailSender;
    $m->sendAcceptMail($email['email'], $username, $amount, 'DepositAccept');

    logAction("DEPOSIT", $username, "Deposit request for ".$amount." accepted.");

} else {
    echo '<p style="text-align: center;">An error occurred... click <a href="../../index.php">here</a> to go back.</p>';
}

?>
</body>
</html>
