<!DOCTYPE html>
<?php
	session_start();
?>

<html>
	<head>
		<title>Add Owner Backend</title>
	</head>

	<body>
		<?php
			//standard error reporting and config setup
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

			//check for malicously direct fed data
			$pid = $_POST["pid"];
			$vemail = $_SESSION["email"];
			$verifyowner = "select count(*) from initiator where email=$1 and projid=$2";
			pg_prepare($dbconn, "verifyowner", $verifyowner);
			$vresult = pg_execute($dbconn, "verifyowner", array($vemail, $pid));
			$vrow = pg_fetch_row($vresult);
			if($vrow[0] != 1)
			{
				echo "You aren't authorized to add owners";
				exit();
			}

			//valid session id# that isn't expired, proceed to add the person as an initiator
			$email = $_POST["email"];
			$ins = "insert into initiator values ($1, $2)";
			pg_prepare($dbconn, "ins", $ins);
			pg_execute($dbconn, "ins", array($pid, $email));
			
			//check if adding the person was successful, perhaps the person is already on the list.
			//need to know to send back the appropriate message to the user
			$verify = "select count(*) from initiator where projid=$1 and email=$2";
			pg_prepare($dbconn, "verify", $verify);
			$result = pg_execute($dbconn, "verify", array($pid, $email));
			$row = pg_fetch_row($result);
			$worked = $row[0];
			if($worked == 1)
			{
				echo "Successfully added";
			}
			else
			{
				echo "Can't add user. Make sure this person isn't already an initiator";
			}
		?>
	</body>
</html>
