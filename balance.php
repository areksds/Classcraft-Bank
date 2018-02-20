<?php require "login/loginheader.php"; 
  if ($userInfo['isBanned'] == 1) {
    header("location:index.php");
  }
  $loans = checkLoans($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Manage Funds</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="css/main.css" rel="stylesheet" media="screen">
  </head>
  <body>
    <div class="container">

      <div class="form-signin">
        <h1 style="text-align: center;">Manage Funds</h1>
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
        <form name="withdrawal" method="post" action="login/withdraw.php">
        <div class="card">
          <h3 class="card-header">Withdraw</h3>
          <div class="card-block">
           <h4 class="card-title">Withdraw from your bank balance into your Classcraft account.</h4>
        <div class="form-group">
    		<label>Amount:</label>
        <?php if ($balance['pendingOut'] > 0){ ?>
        <input type="text" class="form-control" placeholder="Withdrawal pending" readonly>
        <?php } elseif($loans == TRUE) { ?>
         <input type="text" class="form-control" placeholder="Loan pending" readonly>
        <?php } else { ?>
    		<input type="number" class="form-control" id="amount" name="amount" min="0" placeholder="Balance: <?php echo $balance['userBalance'];?>"></input>
        <?php } ?>
        <input type="hidden" name="username" id="username" value="<?php echo $_SESSION['username'];?>">
        <br>
        <?php if ($balance['pendingOut'] > 0){ ?>
          <div class="btn btn-warning">Withdrawal in progress</div>
        <?php } elseif($loans == TRUE) { ?>
         <div class="btn btn-warning">Loan in progress</div>
        <?php } else { ?>
           <button name="submit" id="submit" type="submit" class="btn btn-primary">Request Withdrawal</button>
        <?php } ?>
         </form>
       </div>
     </div>
         <div id="message"></div>
        <form name="deposit" method="post" action="login/deposit.php">
        <div class="card" >
          <h3 class="card-header">Deposit</h3>
          <div class="card-block">
           <h4 class="card-title">Draw from your Classcraft account into your bank account.</h4>
           <div class="form-group">
    		<label>Amount:</label>
        <?php if ($balance['pendingIn'] > 0){ ?>
        <input type="text" class="form-control" placeholder="Deposit pending" readonly>
        <?php } else { ?>
    		<input type="number" class="form-control" id="deposit" name="deposit" placeholder="Max 1000" min="0" max="1000"></input>
        <?php } ?>
        <input type="hidden" name="dusername" id="dusername" value="<?php echo $_SESSION['username'];?>">
        <br>
        <?php if ($balance['pendingIn'] > 0){ ?>
          <div class="btn btn-warning">Deposit in progress</div>
        <?php } else { ?>
          <button name="dsubmit" id="dsubmit" type="submit" class="btn btn-primary">Request Deposit</button>
        <?php } ?>
            </form>
        </div>
      </div>
      <div id="dmessage"></div>
    </div>

        <br>
        <a href="index.php" class="btn btn-primary btn-lg btn-block">Return Home</a>
        <a href="login/logout.php" class="btn btn-default btn-lg btn-block">Logout</a>


    </div> <!-- /container -->

          <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="login/js/jquery-2.2.4.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script type="text/javascript" src="login/js/bootstrap.js"></script>
    <!-- The AJAX send script -->
    <script src="login/js/withdraw.js"></script>
    <script src="login/js/deposit.js"></script>
  </body>
</html>