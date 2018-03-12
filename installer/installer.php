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
				<button type="submit" id="submit" name="submit" class="btn btn-primary">Install database</button>
			</form>
				<br>
				<?php if(isset($_POST['submit'])) {  
					if (($_POST['user'] != '') && ($_POST['host'] != '') && ($_POST['password'] != '') && ($_POST['database'] != '')) {
						
						$sql = file_get_contents('installer.sql');
						$connect = mysqli_connect($_POST['host'], $_POST['user'], $_POST['password']);
						$connect2 = mysqli_select_db($connect, $_POST['database']);
						$result = mysqli_multi_query($connect, $sql);

				if ($connect == false || $connect2 == false || $sql == false || $result == false) {  ?>
					<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Unable to connect to database. Please recheck all the information, and ensure the site directory is writable.</div>				
				<?php } else { 
						$data = "<?php
							//DATABASE CONNECTION VARIABLES
							\$host = \"".$_POST['host']."\"; // Host name 
							\$username = \"".$_POST['user']."\"; // Mysql username 
							\$password = \"".$_POST['password']."\"; // Mysql password 
							\$db_name = \"".$_POST['database']."\"; // Database name

							\$tbl_prefix = \"\";
							\$tbl_members = \$tbl_prefix.\"members\";
							\$tbl_attempts = \$tbl_prefix.\"loginAttempts\";
							\$tbl_balance = \$tbl_prefix.\"balance\";";
						$handleone = fopen('../login/dbconf.php', 'w');
						fwrite($handleone, $data);
						$handletwo = fopen('../login/includes/dbconf.php', 'w');
						fwrite($handletwo, $data);
						if ($handleone == false || $handletwo == false) { ?>
					<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Couldn't write configuration file. Please make sure the directory is writable.</div>	
					<?php } else { {?> 
					<div id="reload" class="alert alert-success"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Configuration written.</div>
					<script type="text/javascript"> location.reload(); </script>
				<?php } } } } else { ?>
					<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Please fill in all the fields.</div>	
				<?php }  } ?>
		</div>
		<?php } elseif (file_exists('../login/config.php') == false || file_exists('../login/includes/config.php') == false) { ?>
		<form name="installer-configuration" method="post" action="">
			<h3>Email Configuration Settings</h3><br>
			<label>Sender Email:</label>
			<input id="email" name="email" type="email" class="form-control" placeholder="noreply@website.net"></input><br>
			<label>Techer email:</label>
			<input id="teacher" name="teacher" type="email" class="form-control" placeholder="teacher@school.com"></input><br>
			<label>SMTP host:</label>
			<input id="host" name="host" type="textarea" class="form-control" placeholder="localhost"></input><br>
			<label>SMTP user:</label>
			<input id="user" name="user" type="textarea" class="form-control" placeholder="root"></input><br>
			<label>SMTP password:</label>
			<input id="password" name="password" type="password" class="form-control" placeholder="password"></input><br>
			<label>SMTP security:</label>
			<select id="security" name="security" class="form-control">
				<option value="">None</option>
				<option value="tls">TLS</option>
				<option value="ssl">SSL</option>
			</select><br>
			<label>SMTP port:</label>
			<input id="port" name="port" type="number" class="form-control" placeholder="25, 456, 587"></input><br>
			<button type="submit" id="submit" name="submit" class="btn btn-primary">Save configuration</button>
		</form> <br>
		<?php if(isset($_POST['submit'])) { 
			if (($_POST['user'] != '') && ($_POST['email'] != '') && ($_POST['teacher'] != '') && ($_POST['password'] != '') && ($_POST['host'] != '') && ($_POST['port'] != '') && ($_POST['password'] != '')) { 
				$length = 10;
				$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				$charactersLength = strlen($characters);
				$code = '';
				for ($i = 0; $i < $length; $i++) {
					$code .= $characters[rand(0, $charactersLength - 1)];
				}
				$data = "<?php
					//Pull '\$base_url' and '\$signin_url' from this file
					include 'globalcon.php';
					//Pull database configuration from this file
					include 'dbconf.php';

					//Set this for global site use
					\$site_name = 'Classcraft Bank';

					//Email of teacher and code for all requests
					\$teacher_email = '".$_POST['teacher']."';
					\$code = '".$code."';

					//Maximum Login Attempts
					\$max_attempts = 5;
					//Timeout (in seconds) after max attempts are reached
					\$login_timeout = 300;

					//ONLY set this if you want a moderator to verify users and not the users themselves, otherwise leave blank or comment out
					\$admin_email = '';
					\$bankusername = 'bank';

					//EMAIL SETTINGS
					//SEND TEST EMAILS THROUGH FORM TO https://www.mail-tester.com GENERATED ADDRESS FOR SPAM SCORE
					\$from_email = '".$_POST['email']."'; //Webmaster email
					\$from_name = 'Classcraft Bank'; //\"From name\" displayed on email

					//Find specific server settings at https://www.arclab.com/en/kb/email/list-of-smtp-and-pop3-servers-mailserver-list.html
					\$mailServerType = 'smtp';
					\$smtp_server = '".$_POST['host']."';
					\$smtp_user = '".$_POST['user']."';
					\$smtp_pw = '".$_POST['password']."';
					\$smtp_port = ".$_POST['port']."; //465 for ssl, 587 for tls, 25 for other
					\$smtp_security = '".$_POST['security']."';//ssl, tls or ''

					//HTML Messages shown before URL in emails (the more
					\$verifymsg = 'Click the link below to verify your new account at the Classcraft Bank:'; //Verify email message
					\$active_email = 'Your new bank account is active! Click this link to log in:';//Active email message
					//LOGIN FORM RESPONSE MESSAGES/ERRORS
					\$signupthanks = 'Thank you for signing up! You will receive a confirmation email shortly.';
					\$activemsg = '<b> Your account has been verified!</b> <br> Please log in at the Classcraft Bank homepage.</a>';

					//DO NOT TOUCH BELOW THIS LINE
					//Unsets \$admin_email based on various conditions (left blank, not valid email, etc)
					if (trim(\$admin_email, ' ') == '') {
						unset(\$admin_email);
					} elseif (!filter_var(\$admin_email, FILTER_VALIDATE_EMAIL) == true) {
						unset(\$admin_email);
						echo \$invalid_mod;
					};
					\$invalid_mod = '\$adminemail is not a valid email address';

					//Makes readable version of timeout (in minutes). Do not change.
					\$timeout_minutes = round((\$login_timeout / 60), 1);";
				
					$handleone = fopen('../login/config.php', 'w');
					fwrite($handleone, $data);
					$handletwo = fopen('../login/includes/config.php', 'w');
					fwrite($handletwo, $data);
				
					if ($handleone == false || $handletwo == false) { ?>
					
						<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Couldn't write configuration file. Please make sure the directory is writable.</div>
					
			<?php } else { ?> 

					<div id="reload" class="alert alert-success"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Configuration written.</div>
					<script type="text/javascript"> location.reload(); </script>
			
			<?php } } else { ?>
				<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Please fill in all the fields.</div>		
			<?php } } } else { ?>	
				<h3 align="center">Installation complete</h3><br>
				<p align="center">Head over to the homepage and register an account to begin. Please remember to set <b>isAdmin</b> to <b>1</b> on administrator accounts in the database.</p><br>
				<div style="text-align: center;"><a href="../index.php" class="btn btn-primary">Homepage</a></div>
			<?php } ?>
	</div>
	 <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="../login/js/jquery-2.2.4.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script type="text/javascript" src="../login/js/bootstrap.js"></script>
	
</body>
</html>