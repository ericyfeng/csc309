<?php
	session_start();
?>

	<?php
		//enable php debugging
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		//database connection variable
		$dbconn = pg_connect("dbname=d8dt3b69jeev6n host=ec2-50-19-249-214.compute-1.amazonaws.com port=5432 user=fhntmyljqrdquf password=vgJO4ZQS8Mi7OceXpIzk_dYL0- sslmode=require");
		//check if email and password match
		$fname = $_POST["fname"];
		$lname = $_POST["lname"];
		$email = $_POST["email"];
		$password = $_POST["password"];
		$confirm = $_POST["confirm"];
		if (empty($fname) || empty($lname) || empty($email) || empty($password) || empty($confirm)) {
			echo "0";
			exit();
		}	

		if($password != $confirm) {
			//echo "<span style=\"color:red\">Your passwords don't match.</span>";
			echo "1";
			exit();
		}
		else {
			$verify = "select count(*) from users where email=$1";
			pg_prepare($dbconn, "verify", $verify);
			$result = pg_execute($dbconn, "verify", array($email));
			$row = pg_fetch_row($result);
			if ($row[0] == 0) {
				$register = "insert into users values ($1, $2, $3, $4, 0, NULL, 0)";
				pg_prepare($dbconn, "signup", $register);
				pg_execute($dbconn, "signup", array($email, $fname, $lname, $password));
				//add the new user to the sessions table
				$addToSess = "insert into session (email) values ($1)";
				pg_prepare($dbconn, "addToSess", $addToSess);
				pg_execute($dbconn, "addToSess", array($email));
				//echo "<span style=\"color:green\">Registration successful</span>";
				//echo 0;
				echo "2";
				exit();
			} 
			else {
				echo "3";
				exit();
				//echo "<span style=\"color:red\">That email is already registered.</span>";
			}
		}
	?>
