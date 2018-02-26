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
		<?php if (file_exists('../login/dbconf.php') == false || file_exists('../login/includes/dbconf.php') == false) { ?>	
		  <form name="installer-mysql" method="post" action="">
				<h3>MySQL Settings</h3>
				<label>Host:</label>
				<input id="host" name="host" type="textarea" class="form-control" placeholder="localhost"></input><br>
				<label>Database:</label>
				<input id="database" name="database" type="textarea" class="form-control" placeholder="classcraft"></input><br>
				<label>User:</label>
				<input id="user" name="user" type="textarea" class="form-control" placeholder="root"></input><br>
				<label>Password:</label>
				<input id="password" name="password" type="password" class="form-control" placeholder="password"></input><br>
				<button type="submit" id="submit" name="submit" class="btn btn-primary">Test connection</button>
			</form>
				<br>
				<?php if(isset($_POST['submit'])) {  
					if (($_POST['user'] != '') && ($_POST['host'] != '') && ($_POST['password'] != '') && ($_POST['database'] != '')) {
						
						// $sql = file_get_contents('installer.sql');
					$sql = "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";
							SET time_zone = \"+00:00\";

							CREATE TABLE activeLoans (
							  username varchar(65) NOT NULL,
							  amount int(65) NOT NULL,
							  date date NOT NULL,
							  interest int(65) NOT NULL,
							  emailed int(1) NOT NULL DEFAULT '0'
							) ENGINE=InnoDB DEFAULT CHARSET=latin1;

							INSERT INTO activeLoans (username, amount, date, interest, emailed) VALUES
							('bank', 0, '9999-12-31', 0, 1);

							CREATE TABLE balance (
							  username varchar(65) NOT NULL,
							  userBalance int(65) NOT NULL,
							  userLoans int(65) NOT NULL,
							  pendingIn int(65) NOT NULL DEFAULT '0',
							  pendingOut int(65) NOT NULL DEFAULT '0'
							) ENGINE=InnoDB DEFAULT CHARSET=latin1;

							INSERT INTO balance (username, userBalance, userLoans, pendingIn, pendingOut) VALUES
							('bank', 0, 0, 0, 0);

							CREATE TABLE loginAttempts (
							  IP varchar(20) NOT NULL,
							  Attempts int(11) NOT NULL,
							  LastLogin datetime NOT NULL,
							  Username varchar(65) DEFAULT NULL,
							  ID int(11) NOT NULL
							) ENGINE=InnoDB DEFAULT CHARSET=utf8;

							CREATE TABLE members (
							  id char(23) NOT NULL,
							  username varchar(65) NOT NULL DEFAULT '',
							  password varchar(65) NOT NULL DEFAULT '',
							  email varchar(65) NOT NULL,
							  verified tinyint(1) NOT NULL DEFAULT '0',
							  mod_timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
							  isAdmin int(1) NOT NULL DEFAULT '0',
							  isBanned int(1) NOT NULL DEFAULT '0'
							) ENGINE=InnoDB DEFAULT CHARSET=utf8;

							CREATE TABLE userLog (
							  id int(10) NOT NULL,
							  action varchar(65) CHARACTER SET utf8 NOT NULL,
							  username varchar(65) CHARACTER SET utf8 NOT NULL,
							  date datetime NOT NULL,
							  notes text CHARACTER SET utf8 NOT NULL
							) ENGINE=InnoDB DEFAULT CHARSET=latin1;

							ALTER TABLE activeLoans
							  ADD PRIMARY KEY (username);

							ALTER TABLE balance
							  ADD PRIMARY KEY (username);

							ALTER TABLE loginAttempts
							  ADD PRIMARY KEY (ID);

							ALTER TABLE members
							  ADD PRIMARY KEY (id),
							  ADD UNIQUE KEY username_UNIQUE (username),
							  ADD UNIQUE KEY id_UNIQUE (id),
							  ADD UNIQUE KEY email_UNIQUE (email);

							ALTER TABLE userLog
							  ADD PRIMARY KEY (id);

							ALTER TABLE loginAttempts
							  MODIFY ID int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

							ALTER TABLE userLog
							  MODIFY id int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=634;";
						$connect = mysqli_connect($_POST['host'], $_POST['user'], $_POST['password']);
						$connect2 = mysqli_select_db($connect, $_POST['database']);
						$result = mysqli_query($connect, $sql);

				if ($connect == false || $connect2 == false || $sql == false || $result == false) {  ?>
					<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Unable to connect to database. Please recheck all the information, and ensure the SQL file in the installer folder is accessible.</div>				
				<?php } else { 
					$e = '';

						$data = "<?php " + "/r/n" +
							"//DATABASE CONNECTION VARIABLES " + "/r/n" +
							"\$host = \"".$_POST['host']."\"; // Host name " + "/r/n" +
							"\$username = \"".$_POST['user']."\"; // Mysql username " + "/r/n" +
							"\$password = \"".$_POST['password']."\"; // Mysql password " + "/r/n" +
							"\$db_name = \"".$_POST['database']."\"; // Database name " + "/r/n" +

							"\$tbl_prefix = \"\";" + "/r/n" +
							"\$tbl_members = \$tbl_prefix.\"members\";" + "/r/n" +
							"\$tbl_attempts = \$tbl_prefix.\"loginAttempts\";" + "/r/n" +
							"\$tbl_balance = \$tbl_prefix.\"balance\";" + "/r/n";
						$handleone = fopen('../login/dbconf.php', 'w');
						fwrite($handleone, $data);
						$handletwo = fopen('../login/includes/dbconf.php', 'w');
						fwrite($handletwo, $data);
						if ($handleone == false || $handletwo == false) { ?>
					<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Couldn't write configuration file. Please make sure the directory is writable.</div>	
					<?php } else { {?> 
					<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Configuration written.</div>
				<?php } } } } else { ?>
					<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Please fill in all the fields.</div>	
				<?php }  } ?>
		</div>
		<?php } else { ?>
		<?php } ?>
	</div>
	 <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="../login/js/jquery-2.2.4.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script type="text/javascript" src="../login/js/bootstrap.js"></script>
</body>
</html>