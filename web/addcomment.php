<!DOCTYPE html>
<?php
	session_start();
?>
<html>
	<body>
		<?php
			//standard error print and config
			date_default_timezone_set("America/Toronto");
			error_reporting(E_ALL);
			ini_set('display_errors', 1);

			$dbconn = pg_connect("dbname=d8dt3b69jeev6n host=ec2-50-19-249-214.compute-1.amazonaws.com port=5432 user=fhntmyljqrdquf password=vgJO4ZQS8Mi7OceXpIzk_dYL0- sslmode=require");

			$sessid = $_POST["sessid"];

			//check if session id is real or faked
			$validnum = "select count(*) from session where sessionid=$1";
			pg_prepare($dbconn, "validnum", $validnum);
			$result = pg_execute($dbconn, "validnum", array($sessid));
			$row = pg_fetch_row($result);
			if($row[0] == 0)
			{
				echo "Please login again"; //do not give hints about the failed login
				exit();
			}

			//check if the valid session id # is expired or not
			$expiry = "select expiration from session where sessionid=$1";
			pg_prepare($dbconn, "expiry", $expiry);
			$result = pg_execute($dbconn, "expiry", array($sessid));
			$row = pg_fetch_row($result);
			$dbdate = new DateTime($row[0]);
			if($dbdate < new DateTime())
			{
				echo "Please login again";
				exit();
			}

			//setup variables for adding a comment
			$email = $_SESSION["email"];
			$projid = $_POST["pid"];
			$comment = $_POST["comment"];
			$fname = $_SESSION["fname"];
			$lname = $_SESSION["lname"];

			//insert the new comment
			$newcomment = "insert into comment (projid, email, comment) values ($1, $2, $3)";
			pg_prepare($dbconn, "newcomment", $newcomment);
			pg_execute($dbconn, "newcomment", array($projid, $email, $comment));
		?>
	</body>
</html>
