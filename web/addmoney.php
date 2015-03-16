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
			date_default_timezone_set("America/Toronto");
			$dbconn = pg_connect("dbname=d8dt3b69jeev6n host=ec2-50-19-249-214.compute-1.amazonaws.com port=5432 user=fhntmyljqrdquf password=vgJO4ZQS8Mi7OceXpIzk_dYL0- sslmode=require");

			//retrieve the donation information from the form POST information that was sent over
			$projid = $_POST["projid"];
			$sessid = $_POST["sessid"];
			$amount = $_POST["amount"];
			$email = $_SESSION["email"]; //email is stored in the php session

			//before doing ANYTHING, check if the amount is at least $1
			if($amount < 1)
			{
				echo "How are you donating a negative amount?";
				exit();
			}

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
