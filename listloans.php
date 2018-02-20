<?php require "login/loginheader.php"; 
  if ($userInfo['isAdmin'] == 0) {
    header("location:index.php");
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Active Loans</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="css/main.css" rel="stylesheet" media="screen">
  </head>
  <body>
  	<div class="container">
  		<h1 style="text-align: center;">Active Loans</h1>
  		<br>
  		<?php 
  		$print = '';
  		$db = new DbConn;
  		$now = time();
	    $stmt = $db->conn->prepare("SELECT * FROM activeLoans WHERE username != 'bank'");
	    $stmt->setFetchMode(PDO::FETCH_OBJ);
	    if($stmt->execute() <> 0)
		{

		    $print .= '<table align="center" class="table table-hover table-responsive">';
		    $print .= '<tr"><th>Username</th>';
		    $print .= '<th>Amount</th>';
        	$print .= '<th>Interest</th>';
		    $print .= '<th>Days Left</th>';

		    while($logs = $stmt->fetch()) // loop and display data
		    {

	          $seconddate = strtotime($logs->date);
			  $datediff2 = $now - $seconddate;
		      $datediff1 = floor($datediff2 / (60 * 60 * 24));
		      $datediff = 14 - $datediff1;

          if ($datediff < 0){
            $datediff = 0;
          }
          
		        $print .= '<tr>';
		        $print .= "<td>{$logs->username}</td>";
		        $print .= "<td>{$logs->amount}</td>";
            	$print .= "<td>{$logs->interest}</td>";
		        $print .= "<td>{$datediff}</td>";
		        $print .= '</tr>';

		    }

		    $print .= "</table>";
		    echo $print;
		}

  		?>
  		<br>
  		<div class="form-signin">
  		<a href="admin.php" class="btn btn-primary btn-lg btn-block">Return to Admin Panel</a>
        <a href="login/logout.php" class="btn btn-default btn-lg btn-block">Logout</a>
    </div>
  	</div>
  </body>
</html>