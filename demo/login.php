<?php
	session_start();
?>

<html>
	<head>
		<title>Secure Login Processing</title>
	</head>
	
	<body>
	<?php
		//enable php debugging
		error_reporting(E_ALL);
		ini_set('display_errors', 1);

		//database connection variable
		$dbconn = pg_connect("dbname=cs309 user=Daniel");

		//check if email and password match
		$email = $_POST["email"];
		$passwd = $_POST["passwd"];
		$verify = "select count(*) from users where email=$1 and password=$2";
		pg_prepare($dbconn, "preparelogin", $verify);
		$result = pg_execute($dbconn, "preparelogin", array($email, $passwd));
		$row = pg_fetch_row($result);

		if($row[0])
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
			$getname = "select fname, lname from users where email=$1";
			pg_prepare($dbconn, "getname", $getname);
			$result = pg_execute($dbconn, "getname", array($email));
			$row = pg_fetch_row($result);
			$fname=$row[0];
			$lname=$row[1];

			//set session variables that are reused on almost every webpage
			$_SESSION["fname"] = $fname;
			$_SESSION["lname"] = $lname;
			$_SESSION["email"] = $email;

			//redirect user to dashboard on successful login
			header("Location: dashboard.php?sessid=$rand");
			exit();
		}
		else
		{
			echo "Please login again";
		}
	?>
	</body>
</html>





