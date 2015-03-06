<!DOCTYPE html>
<?php
	session_start();
?>

<html>
	<head>
		<title>Simple Add Owner</title>
	</head>

	<body>
		<?php
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
			$dbconn = pg_connect("dbname=cs309 user=Daniel");
			date_default_timezone_set('America/Toronto');

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

			$pid = $_POST["pid"];
			$email = $_POST["email"];
			$ins = "insert into initiator values ($1, $2)";
			pg_prepare($dbconn, "ins", $ins);
			pg_execute($dbconn, "ins", array($pid, $email));
			
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
