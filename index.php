<?php require "login/loginheader.php"; ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Bank Homepage</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="css/main.css" rel="stylesheet" media="screen">
  </head>
  <body>
    <div class="container">
      <?php if ($userInfo['isBanned'] == 1) { ?>
      <div class="form-signin">
        <h1 style="text-align: center; ">You have been <div style="color: red;">banned</div> from using the bank.</h1>
        <br>
        <h4 style="text-align: center;">Please consult the administrator for more information.</h4>
        <br>
      <?php } else { ?>
      <div class="form-signin">
        <h1 style="text-align: center;">Welcome to the Classcraft Bank!</h1>
        <br>
        <div class="alert alert-info" role="alert">
          <h4 class="alert-heading">Bank balance: <?php echo $balance['userBalance'];?> GP</h4>
        </div>
        <?php if ($balance['pendingOut'] > 0){ ?>
        <div class="alert alert-warning" role="alert">
          <h4 class="alert-heading">Pending withdrawals: <?php echo $balance['pendingOut'];?> GP</h4>
        </div>
        <?php } ?>
        <?php if ($balance['pendingIn'] > 0){ ?>
        <div class="alert alert-warning" role="alert">
          <h4 class="alert-heading">Pending deposits: <?php echo $balance['pendingIn'];?> GP</h4>
        </div>
        <?php } ?>
        <div class="card">
          <h3 class="card-header">Withdraw/Deposit Gold</h3>
          <div class="card-block">
           <h4 class="card-title">Add or remove from your balance</h4>
           <p class="card-text">You can manage your bank balance here.</p>
           <a href="balance.php" class="btn btn-primary">Balance</a>
        </div>
        <div class="card">
          <h3 class="card-header">Request Loan</h3>
          <div class="card-block">
           <h4 class="card-title">Short on gold? Request a loan!</h4>
           <p class="card-text">All requests will be checked before being accepted.</p>
           <a href="loans.php" class="btn btn-primary">Loans</a>
        </div>
        <div class="card" >
          <h3 class="card-header">Send Money</h3>
          <div class="card-block">
           <h4 class="card-title">Send money to friends!</h4>
           <p class="card-text">Do your friends need money? Send some!</p>
           <a href="send.php" class="btn btn-primary">Send Money</a>
        </div>
        <br>
      </div>
        <?php if ($userInfo['isAdmin'] == 1){ ?>
          <a href="admin.php" class="btn btn-success btn-lg btn-block">Admin Panel</a>
        <?php } } ?>
        <a href="login/logout.php" class="btn btn-default btn-lg btn-block">Logout</a>
      </div>
    </div> <!-- /container -->
  </body>
</html>
