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
    <title>Loan Center</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="css/main.css" rel="stylesheet" media="screen">
  </head>
  <body>
    <div class="container">

      <div class="form-signin">
        <?php if ($balance['userLoans'] > 0) { ?>
        <h1 style="text-align: center;">Loan Center</h1>
        <br>
          <h4 style="text-align: center;">You will recieve an email once your loan (<?php echo $balance['userLoans'];?> GP) is approved. You cannot take another loan out until the previous one is repaid.</h4>
          <br>
        <?php } elseif ($balance['pendingOut'] > 0) { ?>
        <h1 style="text-align: center;">Loan Center</h1>
        <br>
          <h4 style="text-align: center;">You cannot take out a loan until your pending withdrawal is processed.</h4>
          <br>
        <?php } elseif ($loans == TRUE) { 

          $now = time();

          $bdb = new DbConn;
          $bstmt = $bdb->conn->prepare("SELECT * FROM activeLoans WHERE username = :myusr");
          $bstmt->bindParam(':myusr', $_SESSION['username']);
          $bstmt->execute();
          $info = $bstmt->fetch(PDO::FETCH_ASSOC);
          $seconddate = strtotime($info['date']);
          $datediff2 = $now - $seconddate;
          $datediff1 = floor($datediff2 / (60 * 60 * 24));
          $datediff = 14 - $datediff1;
          $final = $info['amount'] + $info['interest'];

          ?>
        <h1 style="text-align: center;">Loan Center</h1>
        <br>
        <?php if ($datediff > 0) { ?>
          <div style="text-align: center;" class="alert alert-danger" role="alert">
          <h4 class="alert-heading">You have</h4>
          <h2 class="alert-heading"><?php echo $datediff;?> day<?php if ($datediff > 1) { ?>s<?php } ?></h2>
          <h4 class="alert-heading">to pay back your loan.</h4>
        </div>
        <br>
        <div style="text-align: center;" class="alert alert-warning" role="alert">
          <h4 class="alert-heading">Current payback: <?php echo $final;?> GP</h4>
        </div>
        <?php if ($balance['userBalance'] >= $final) { ?>
        <h5 style="text-align: center;">You have enough in your balance to pay back your loan.</h5>
        <br>
        <button name="pay" id="pay" class="btn btn-success btn-lg btn-block">Pay Back Loan</button>
        <br>
        <div id="paymessage"></div>
        <br>
        <?php } else { ?>
        <h5 style="color: red; text-align: center;">You don't have enough in your balance to pay back your loan!</h5>
        <br>
        <a href="balance.php" class="btn btn-primary btn-lg btn-block">Deposit Gold</a>
        <br>
        <?php } } else { ?>
          <div style="text-align: center;" class="alert alert-danger" role="alert">
          <h2 class="alert-heading">Your pending loan is overdue.</h2>
        </div>
        <br>
        <div style="text-align: center;" class="alert alert-warning" role="alert">
          <h4 class="alert-heading">Current payback: <?php echo $final;?> GP</h4>
        </div>
            <?php if ($balance['userBalance'] >= $final) { ?>
        <h5 style="text-align: center;">You have enough in your balance to pay back your loan.</h5>
        <br>
            <button name="pay" id="pay" class="btn btn-success btn-lg btn-block">Pay Back Loan</button>
            <br>
            <div id="paymessage"></div>
            <br>
            <?php } else { ?>
            <p style="color: red; text-align: center;">You don't have enough in your balance to pay back your loan!</p>
            <br>
            <a href="balance.php" class="btn btn-primary btn-lg btn-block">Deposit Gold</a>
            <br>
        <?php } } } else { ?>
        <form class="form-signin" name="send" method="post" action="">
        <div class="card">
          <h1 style="text-align: center;">Loan Center</h1>
          <h3 class="card-header" style="text-align: center;">Request Loan</h3>
          <div class="card-block">
           <div class="form-group">
            <br>
        <label>Amount:</label>
        <input type="number" name="amount" id="amount" class="form-control" min="1" max="500" placeholder="Maximum: 500"></input>
        <br>
      <div id="calculate" style="display: none"> 
        <label>Calculated Interest:</label>
        <input type="text" class="form-control" name="interest" id ="interest"  value="" readonly>
        <br> 
        <label>Final Amount Due:</label>
        <input type="text" class="form-control" name="final" id ="final"  value="" readonly>
        <br> 
        <label>Why do you want this loan?</label>
    <textarea class="form-control" name="freason" id ="freason"  placeholder="Well, what's your reason?" required></textarea>
    <br>
       <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#request">Request Loan</button>
        </div>  
      </form>
        </div>
        <div class="modal fade" id="request" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirmation</h5>
          </div>
          <div class="modal-body">
            <form method="post" action="login/loans.php">
              <div class="form-group">
                
              </div>
                <h4 style="color: red;">You will be responsible for paying back <b id="finalshow"></b> gold pieces within 2 weeks. Failure to do so will result in your character's death in Classcraft.</h4>
          </div>
          <div class="modal-footer">
            <input type="hidden" name="famount" id ="famount"  value="">
            <input type="hidden" name="reason" id ="reason"  value="">
            <button name="submit" id="submit" class="btn btn-primary btn-lg btn-block">Accept</button>
            <button class="btn btn-default btn-lg btn-block" data-dismiss="modal">Cancel</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div id="message"></div>

    <?php } ?>
            

        <a href="index.php" class="btn btn-primary btn-lg btn-block">Return Home</a>
        <a href="login/logout.php" class="btn btn-default btn-lg btn-block">Logout</a>

      </div>
        
      </div>
    </div> <!-- /container -->
            <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="login/js/jquery-2.2.4.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script type="text/javascript" src="login/js/bootstrap.js"></script>
    <!-- The AJAX script -->
    <script src="login/js/loans.js"></script>

    <script>
    $("#amount").on('change', function() {
      $("#calculate").hide()
      if($(this).val() !== "" && $(this).val() !== "0" && $(this).val() <= 500){
        if (/^\d+$/.test($(this).val())) {
          $("#calculate").show()

          var amount = parseInt($(this).val());
          var interest = parseInt(amount / 5);
          var interest = parseInt(Math.round(interest));
          var final = interest + amount;

          document.getElementById("interest").value = interest;
          document.getElementById("final").value = final;
          document.getElementById("finalshow").innerHTML = final;
          document.getElementById("famount").value = amount;
        }
        } 
    })


    $("#freason").on('change', function() {
      var reason = document.getElementById("freason").value;
      document.getElementById("reason").value = reason;
    })


  </script>
  </body>


</html>
