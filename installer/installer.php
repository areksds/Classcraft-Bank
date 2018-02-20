<?php ?>

<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Installer</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="../css/bootstrap.css" rel="stylesheet" media="screen">
<link href="../css/main.css" rel="stylesheet" media="screen">
</head>
<body>
	<div class="container">
		<div class="form-signin">
			<h1 style="text-align: center;">Classcraft Bank Installer</h1> <br>
			
		  <form name="installer-mysql" method="post" action="">
				<h3>MySQL Settings</h3>
				<label>Host:</label>
				<input id="host" name="host" type="textarea" class="form-control" placeholder="127.0.01"></input><br>
				<label>Database:</label>
				<input id="database" name="database" type="textarea" class="form-control" placeholder="classcraft"></input><br>
				<label>User:</label>
				<input id="user" name="user" type="textarea" class="form-control" placeholder="root"></input><br>
				<label>Password:</label>
				<input type="password" class="form-control" placeholder="password"></input><br>
				<button class="btn btn-primary">Test connection</button>
			</form>
		</div>
		
	</div>
</body>
</html>