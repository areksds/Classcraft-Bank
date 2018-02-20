<?php require "login/loginheader.php"; 
  if ($userInfo['isAdmin'] == 0) {
    header("location:index.php");
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="css/main.css" rel="stylesheet" media="screen">
  </head>
  <body>
    <div class="container">

      <div class="form-signin">
        <h1 style="text-align: center;">Admin Panel</h1>
        <br>
        <div class="alert alert-info" role="alert">
          <h4 class="alert-heading">Overall bank funds: <?php $bank = checkBalance('bank'); echo $bank['userBalance'];?></h4>
        </div>
        <div class="alert alert-info" role="alert">
          <h4 class="alert-heading">Accumulated interest: <?php $coolinterest = checkInterest(); echo $coolinterest['interest'];?></h4>
        </div>
        <form name="panel" method="post" action="">
        <div class="card">
          <h3 class="card-header">Manage Accounts</h3>
          <div class="card-block">
           <h4 class="card-title">Choose a function to use</h4>
        <div class="form-group">
        	<label>Function:</label>
    		<select class="form-control" name="function" id="function">
    			<option value="">Select Function</option>
  				<option value="balance">Check Balance</option>
  				<option value="set">Set Balance</option>
  				<option value="change">Add/Subtract from Balance</option>
  				<option value="ban">Ban</option>
          <option value="liftban">Lift Ban</option>
          <option value="interest">Distribute Interest</option>
			</select>
			<br>
			<div style="display:none" id="usernamefield">
			<label>Username:</label>
        <select class="form-control" name="username" id="username">
          <option value="">Select User</option>
          <?php 
            $print = '';
            $db = new DbConn;
            $stmt = $db->conn->prepare("SELECT * FROM members WHERE username != 'bank'");
            $stmt->setFetchMode(PDO::FETCH_OBJ);
            if($stmt->execute() <> 0)
          {

              while($usernames = $stmt->fetch()) // loop and display data
              {

                  $print .= "<option value=\"{$usernames->username}\">{$usernames->username}</option>";
                  
              }

              echo $print;
          }

            ?>
        </select>
        <br></div>

    		<div style="display:none" id="newbalance">
			<label>New balance:</label>
    		<input type="number" class="form-control" id="newbalance" name="newbalance" placeholder="Balance"></input><br></div>

    		<div style="display:none" id="changebalance">
			<label>Adjust balance:</label>
    		<input type="number" class="form-control" id="changebalance" name="changebalance" placeholder="Adjust Balance"></input><br></div>

    		<div style="display:none" id="reason">
			<label>Ban reason:</label>
    		<input type="textarea" class="form-control" id="reason" name="reason" placeholder="Reason"></input><br></div>

        <div id="button">
           <button name="submit" id="submit" type="submit" class="btn btn-primary">Run Function</button></div>

        <div style="display:none" id="otherbutton">
          <form name="interestgive" method="post" action="login/interest.php">
           <button name="distribute" id="distribute" type="submit" class="btn btn-primary">Distribute Interest</button></div>
           <div id="modal"></div>
         </form>
            <br>
           <?php if(isset($_POST['submit'])) { if ($_POST['function'] == 'balance' && $_POST['username'] != '') { $userBalance = checkBalance($_POST['username']); ?>
           <?php if($userBalance['userBalance'] != ''){ ?>
           <div class="alert alert-info alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Balance of <b><?php echo $_POST['username']; ?></b>: <?php echo $userBalance['userBalance']; ?> </div>

           <?php } else {?>
           <div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>The specified user could not be found.</div>
           <?php } } elseif ($_POST['function'] == 'set' && $_POST['username'] != '' && $_POST['newbalance'] != '') { 
           	$userBalance = checkBalance($_POST['username']);

           	if($userBalance['userBalance'] == ''){ ?>
				<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>The specified user could not be found.</div>

           	<?php } else {

           	if ($_POST['username'] == "bank") { ?>

	            <div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>You cannot update the bank balance.</div>

           	<?php } elseif($_POST['newbalance'] < 0) { ?>

              <div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>The new balance must be greater than 0.</div>

            <?php } else{ 
	           	$bankUsername = "bank";
	           	$bankBalance = checkBalance("bank");
	           	$nearamount = $_POST['newbalance'] - $userBalance['userBalance'];
	           	$bankamount = $nearamount + $bankBalance['userBalance'];

	           	$bdb = new DbConn;
	            $bstmt = $bdb->conn->prepare("UPDATE balance SET userBalance = :balance WHERE username = :myusr");
	            $bstmt->bindParam(':balance', $_POST['newbalance']);
	            $bstmt->bindParam(':myusr', $_POST['username']);
	            $bstmt->execute();

	            $vdb = new DbConn;
	            $vstmt = $vdb->conn->prepare("UPDATE balance SET userBalance = :balance WHERE username = :myusr");
	            $vstmt->bindParam(':balance', $bankamount);
	            $vstmt->bindParam(':myusr', $bankUsername);
	            $vstmt->execute();

              logAction("SET BALANCE", $_POST['username'], $_SESSION['username']." just set ".$_POST['username']."'s balance to ".$_POST['newbalance']);

            ?>

            <div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Balance of <b><?php echo $_POST['username']; ?></b> successfully updated from <b><?php echo $userBalance['userBalance']; ?></b> to <b><?php echo $_POST['newbalance']; ?></b>. </div>

    <?php } } } elseif($_POST['function'] == 'change' && $_POST['username'] != '' && $_POST['changebalance'] != '') {  

          $userBalance = checkBalance($_POST['username']);

          if($userBalance['userBalance'] == ''){ ?>

        <div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>The specified user could not be found.</div>

            <?php } else {

            if ($_POST['username'] == "bank") { ?>

              <div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>You cannot update the bank balance.</div>

            <?php } elseif (($_POST['changebalance'] + $userBalance['userBalance']) < 0) { ?>

            <div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>You can't set the user's balance to less than 0.</div>

            <?php } else {

              $newamount = $_POST['changebalance'] + $userBalance['userBalance'];
              $bankUsername = "bank";
              $bankBalance = checkBalance("bank");
              $bankamount = $_POST['changebalance'] + $bankBalance['userBalance'];

              $bdb = new DbConn;
              $bstmt = $bdb->conn->prepare("UPDATE balance SET userBalance = :balance WHERE username = :myusr");
              $bstmt->bindParam(':balance', $newamount);
              $bstmt->bindParam(':myusr', $_POST['username']);
              $bstmt->execute();

              $vdb = new DbConn;
              $vstmt = $vdb->conn->prepare("UPDATE balance SET userBalance = :balance WHERE username = :myusr");
              $vstmt->bindParam(':balance', $bankamount);
              $vstmt->bindParam(':myusr', $bankUsername);
              $vstmt->execute(); 

              logAction("CHANGE BALANCE", $_POST['username'], $_SESSION['username']." just changed ".$_POST['username']."'s balance by ".$_POST['changebalance']);

              ?>

              <div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Balance of <b><?php echo $_POST['username']; ?></b> successfully updated to <b><?php echo $newamount; ?></b> (changed by <b><?php echo $_POST['changebalance']; ?></b> GP). </div>

            <?php } } } elseif ($_POST['function'] == 'ban' && $_POST['username'] != '' && $_POST['reason'] != '') {

              $unluckyMan = userInfo($_POST['username']);

              if ($_POST['username'] == "bank") { ?>

              <div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>You cannot ban the bank.</div>

            <?php } elseif ($unluckyMan['isAdmin'] == 1) { ?>

            <div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>You cannot ban an administrator.</div>

            <?php } elseif ($unluckyMan['isBanned'] == 1) { ?>

            <div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>This user is already banned.</div>

            <?php } elseif($unluckyMan['isBanned'] == '') { ?>

            <div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>The specified user could not be found.</div>

            <?php } else {

              $vdb = new DbConn;
              $vstmt = $vdb->conn->prepare("UPDATE members SET isBanned = 1 WHERE username = :myusr");
              $vstmt->bindParam(':myusr', $_POST['username']);
              $vstmt->execute(); 

              logAction("BAN", $_POST['username'], $_POST['username']." was banned for the reason: ".$_POST['reason']);

              ?>

              <div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b><?php echo $_POST['username']; ?></b> was banned for the reason: <?php echo $_POST['reason']; ?>. </div>

     <?php } } elseif ($_POST['function'] == 'liftban' && $_POST['username'] != '') {

        $luckyMan = userInfo($_POST['username']);

        if ($luckyMan['isBanned'] == 0){ ?>

        <div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>This user is not banned.</div>

       <?php } elseif ($luckyMan['isBanned'] == '') { ?>

            <div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>The specified user could not be found.</div>

        <?php } else {

              $vdb = new DbConn;
              $vstmt = $vdb->conn->prepare("UPDATE members SET isBanned = 0 WHERE username = :myusr");
              $vstmt->bindParam(':myusr', $_POST['username']);
              $vstmt->execute(); 

              logAction("LIFT BAN", $_POST['username'], $_POST['username']." was unbanned.");

              ?>

              <div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Ban on <b><?php echo $_POST['username']; ?></b> lifted.</div>

      <?php } } } ?>


         </form>
        <br>
        <a href="listloans.php" class="btn btn-primary btn-lg btn-block">Active Loans</a>
        <a href="logs.php" class="btn btn-primary btn-lg btn-block">View User Logs</a>
        <a href="index.php" class="btn btn-primary btn-lg btn-block">Return Home</a>
        <a href="login/logout.php" class="btn btn-default btn-lg btn-block">Logout</a>

      </div>
      	
      </div>
    </div> <!-- /container -->
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="login/js/jquery-2.2.4.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script type="text/javascript" src="login/js/bootstrap.js"></script>

    <script>
		$("#function").on('change', function() {
			$("#usernamefield").hide()
			$("#newbalance").hide()
			$("#changebalance").hide()
			$("#reason").hide()
      $("#otherbutton").hide()
      $("#button").show()
			if( $(this).val() === "balance" || $(this).val() === "set" || $(this).val() === "change" || $(this).val() === "ban" || $(this).val() === "liftban"){
    			$("#usernamefield").show()
    			if ($(this).val() === "set"){
    				$("#newbalance").show()
    			} 
    			if ($(this).val() === "change"){
    				$("#changebalance").show()
    			} 
    			if ($(this).val() === "ban"){
    				$("#reason").show()
    			} 
          
    		}  else { 

        if($(this).val() === "interest"){
          $("#button").hide()
          $("#otherbutton").show()
        }
          
        } 
		})
	</script>

  <!-- The AJAX script -->
  <script src="login/js/interest.js"></script>
  </body>
</html>
