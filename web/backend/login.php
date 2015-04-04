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
		$email = $_POST["email"];
		$password = $_POST["password"];
		if (empty($email) || empty($password)) {
			echo "0";
			exit();
		}			

		$verify = "select 1 from users where email=$1 and password=$2";
		pg_prepare($dbconn, "preparelogin", $verify);
		$result = pg_execute($dbconn, "preparelogin", array($email, $password));
		$row = pg_fetch_row($result);

		if($row[0] == 1)
		{
			//set secure database session information to prevent faking in "GET/POST" information
			$sessionid="select round(random()*10^9), (current_timestamp + '4 hours')";
			pg_prepare($dbconn, "sessionid", $sessionid);
			$result = pg_execute($dbconn, "sessionid", array());
			$row = pg_fetch_row($result);
			$rand = $row[0];
			$exp = $row[1];
			$setdb = "update session set sessionid=$1, expiration=$2 where email=$3";
			pg_prepare($dbconn, "setdb", $setdb);
			pg_execute($dbconn, "setdb", array($rand, $exp, $email));

			//get first name and last name for php session information to prevent constantly dialing into database
			$getname = "select fname, lname, admin from users where email=$1";
			pg_prepare($dbconn, "getname", $getname);
			$result = pg_execute($dbconn, "getname", array($email));
			$row = pg_fetch_row($result);
			$fname=$row[0];
			$lname=$row[1];

			//set session variables that are reused on almost every webpage
			$_SESSION["fname"] = $fname;
			$_SESSION["lname"] = $lname;
			$_SESSION["email"] = $email;
			$_SESSION["admin"] = $row[2];
			$_SESSION["sessid"] = $rand;
			//redirect user to dashboard on successful login
			echo "2";
			exit();
		}
		else
		{
			echo "1";
			exit();
		}
	?>


