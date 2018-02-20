<?php require "login/loginheader.php"; 
  if ($userInfo['isBanned'] == 1) {
    header("location:index.php");
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Send Money</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="css/main.css" rel="stylesheet" media="screen">
  </head>
  <body>
    <div class="container">

      <form class="form-signin" name="send" method="post" action="login/send.php">
        <h1 style="text-align: center;">Send Money</h1>
        <div class="card">
          <h3 class="card-header" style="text-align: center;">Send Money to Friends</h3>
          <div class="card-block">
           <div class="form-group">
            <br>
        <label>From user:</label>
        <input type="text" name="from" id="from" class="form-control" value="<?php echo $_SESSION['username'];?>" readonly>
        <br>
        <label>Recipient email:</label>
        <input type="email" class="form-control" name="email" id ="email"  placeholder="student@email.com"></input>
        <br>    
    		<label>Send amount:</label>
    		<input type="number" class="form-control" name="amount" id="amount" placeholder="Balance: <?php echo $balance['userBalance'] - $balance['pendingOut']; if ($balance['pendingOut'] > 0){ echo " (withdrawals deducted)"; }?>" min="0" max="<?php echo $balance['userBalance'] - $balance['pendingOut'];?>"></input>
        <br>
        <button name="Submit" id="submit" style="text-align: center;" class="btn btn-primary" type="submit">Send Money</button>
  		</form>
           <div id="message"></div>
        </div>
        <br>
        <a href="index.php" class="btn btn-primary btn-lg btn-block">Return Home</a>
        <a href="login/logout.php" class="btn btn-default btn-lg btn-block">Logout</a>
      </div>
      	
      </div>
    </div> <!-- /container -->

      <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="login/js/jquery-2.2.4.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script type="text/javascript" src="login/js/bootstrap.js"></script>
    <!-- The AJAX send script -->
    <script src="login/js/send.js"></script>

  </body>
</html>