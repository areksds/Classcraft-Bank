<?php require "login/loginheader.php"; 
  if ($userInfo['isAdmin'] == 0) {
    header("location:index.php");
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>User Logs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="css/main.css" rel="stylesheet" media="screen">
  </head>
  <body>
  	<div class="container">
  		<h1 style="text-align: center;">User Logs</h1>
  		<br>
  		<?php 
  		$print = '';
  		$db = new DbConn;
	    $stmt = $db->conn->prepare("SELECT * FROM userLog ORDER BY id DESC;");
	    $stmt->setFetchMode(PDO::FETCH_OBJ);
	    if($stmt->execute() <> 0)
		{

		    $print .= '<table align="center" class="table table-hover table-responsive">';
		    $print .= '<tr"><th>ID</th>';
		    $print .= '<th>Action</th>';
		    $print .= '<th>Username</th>';
		    $print .= '<th>Date</th>';
		    $print .= '<th>Notes</th></tr>';

		    while($logs = $stmt->fetch()) // loop and display data
		    {

		        $print .= '<tr>';
		        $print .= "<td>{$logs->id}</td>";
		        $print .= "<td>{$logs->action}</td>";
		        $print .= "<td>{$logs->username}</td>";
		        $print .= "<td>{$logs->date}</td>";
		        $print .= "<td>{$logs->notes}</td>";
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