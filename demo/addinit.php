<!DOCTYPE html>
<html>
	<head>
		<title>Simple Add Owner</title>
	</head>

	<body>
		<?php
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
			$dbconn = pg_connect("dbname=cs309 user=Daniel");

			$id = $_POST["id"];
			$email = $_POST["email"];
			$ins = "insert into initiator values ($1, $2)";
			pg_prepare($dbconn, "ins", $ins);
			pg_execute($dbconn, "ins", array($id, $email));
			
			$verify = "select count(*) from initiator where projid=$1 and email=$2";
			pg_prepare($dbconn, "verify", $verify);
			$result = pg_execute($dbconn, "verify", array($id, $email));
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
