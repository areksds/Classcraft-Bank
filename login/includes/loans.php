<!DOCTYPE html>
<html>
  <head>
    <link href="../../css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="../../css/main.css" rel="stylesheet" media="screen">
    <meta charset="UTF-8">
    <title>Loan Request</title>
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
$bankamount = $bank['userBalance'] - $amount;

if ($code != $verification) {
    echo 'Request authentication error. Please contact an administrator for more information.';
} elseif ($balance['userLoans'] <= 0) {
    echo 'Request invalid.';
} elseif ($status == 0) { 
    
    if(isset($_POST['submit'])){ 
        $input = $_POST['reason'];
        if ($input == ""){
             $message = "<p style=\"color: red; text-align: center;\">Please enter a reason.</p>";
        } else {
            $message = "<p style=\"color: green; text-align: center;\">Response recorded. You may now close this page.</p>";

            $bdb = new DbConn;
            $bstmt = $bdb->conn->prepare("UPDATE balance SET userLoans = :pending WHERE username = :username");
            $bstmt->bindParam(':pending', $clearPending);
            $bstmt->bindParam(':username', $username);
            $bstmt->execute();

            $m = new MailSender;
            $m->sendReasonMail($email['email'], $username, $amount, $input, 'LoanReject');

            logAction("LOAN REJECT", $username, "Loan request for ".$amount." GP rejected for reason: ".$input);
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
    Reason: <input type="text" name="reason" id="reason" placeholder="I don't feel like it"></input>
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
    
    $raw = $amount / 5;
    $interest = round($raw);
    $date = date('Y/m/d');
    $bank = "bank";

    $bdb = new DbConn;
    $bstmt = $bdb->conn->prepare("UPDATE balance SET userLoans = :pending WHERE username = :username");
    $bstmt->bindParam(':pending', $clearPending);
    $bstmt->bindParam(':username', $username);
    $bstmt->execute();

    $db = new DbConn;
    $stmt = $bdb->conn->prepare("UPDATE balance SET userBalance = :amount WHERE username = :username");
    $stmt->bindParam(':amount', $bankamount);
    $stmt->bindParam(':username', $bank);
    $stmt->execute();

    $vdb = new DbConn;
    $vtmt = $vdb->conn->prepare("INSERT INTO activeLoans (username, amount, date, interest)
    VALUES (:username, :amount, :due, :interest)");
    $vtmt->bindParam(':username', $username);
    $vtmt->bindParam(':amount', $amount);
    $vtmt->bindParam(':due', $date);
    $vtmt->bindParam(':interest', $interest);
    $vtmt->execute();

    $m = new MailSender;
    $m->sendAcceptMail($email['email'], $username, $amount, 'LoanAccept');

    logAction("LOAN", $username, "Loan request for ".$amount." GP accepted with ".$interest." GP as interest.");

} else {
    echo '<p style="text-align: center;">An error occurred... click <a href="../../index.php">here</a> to go back.</p>';
}

?>
</body>
</html>
