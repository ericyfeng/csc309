<?php
	session_start();
?>

<html>
	<head>
		<title>Sign up processing</title>
	</head>
	
	<body>
	<?php
		//enable php debugging
		error_reporting(E_ALL);
		ini_set('display_errors', 1);

		//database connection variable
		$dbconn = pg_connect("dbname=cs309 user=Daniel");

		//check if email and password match
		$fname = $_POST["fname"];
		$lname = $_POST["lname"];
		$email = $_POST["email"];
		$passwd = $_POST["passwd"];
		$confirm = $_POST["confirm"];
		if($passwd != $confirm)
		{
			echo "<span style=\"color:red\">Your passwords don't match.</span>";
			exit();
		}
		$verify = "select count(*) from users where email=$1";
		pg_prepare($dbconn, "verify", $verify);
		$result = pg_execute($dbconn, "verify", array($email));
		$row = pg_fetch_row($result);
		if ($row[0] == 0) {
			$register = "insert into users values ($1, $2, $3, $4, 0);";
			pg_prepare($dbconn, "signup", $register);
			pg_execute($dbconn, "signup", array($email, $fname, $lname, $passwd));

			//add the new user to the sessions table
			$addToSess = "insert into session (email) values ($1)";
			pg_prepare($dbconn, "addToSess", $addToSess);
			pg_execute($dbconn, "addToSess", array($email));

			echo "<span style=\"color:green\">Registration successful</span>";
		} 
		else {
			echo "<span style=\"color:red\">That email is already registered.</span>";
		}
	?>
	</body>
</html>





