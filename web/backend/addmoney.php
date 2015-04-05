<!DOCTYPE html>
<?php
	session_start();
?>

<html>
	<head>
		<title>Add Donation Backend</title>
	</head>

	<body>
		<?php
			include("checksession.php");
			$projid = $_POST["projid"];
			$amount = $_POST["amount"];
			$email = $_SESSION["email"];

			//get total amount in the project
			$curr = " select curramount from project where projid=$1";
			pg_prepare($dbconn, "curr", $curr);
			$result = pg_execute($dbconn, "curr", array($projid));
			$row = pg_fetch_row($result);
			//make the new amount the total amount + donation
			$newamount = $row[0] + $amount;

			//write the new amount back to the db
			$syncdb = "update project set curramount=$1 where projid=$2";
			pg_prepare($dbconn, "syncdb", $syncdb);
			$result = pg_execute($dbconn, "syncdb", array($newamount, $projid));

			//log the donation in the funder table
			$logdonation = "insert into funder (email, projid, datestamp, amount) values ($1, $2, $3, $4)";
			pg_prepare($dbconn, "logdonation", $logdonation);
			pg_execute($dbconn, "logdonation", array($email, $projid, date("Y-m-d"), $amount));

			//send back the new amount so it can be updated
			echo $newamount;
		?>
	</body>
</html>
