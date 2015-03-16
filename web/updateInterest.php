<!DOCTYPE html>
<?php
	session_start();
?>

<html>
	<body>
		<?php
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
			date_default_timezone_set('America/Toronto');
			$dbconn = pg_connect("dbname=d8dt3b69jeev6n host=ec2-50-19-249-214.compute-1.amazonaws.com port=5432 user=fhntmyljqrdquf password=vgJO4ZQS8Mi7OceXpIzk_dYL0- sslmode=require");

			//check if session id is real or faked
			$sessid = $_POST["sessid"];
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

			$commid = $_POST["commid"];
			$email = $_SESSION["email"];

			//insert the user's new interest
			$insert = "insert into personalinterests values ($1, $2)";
			pg_prepare($dbconn, "insert", $insert);
			pg_execute($dbconn, "insert", array($email, $commid));

			//return a list of all the interests for the my profile page
			$myinterest = "select description from personalinterests natural join community where email=$1";
			pg_prepare($dbconn, "myinterest", $myinterest);
			$result = pg_execute($dbconn, "myinterest", array($email));
			echo "<ul>";
			while ($row = pg_fetch_row($result))
			{
				echo "<li>$row[0]</li>";
			}
			echo "</ul>";
		?>
	</body>
</html>
