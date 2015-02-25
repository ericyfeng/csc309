<!DOCTYPE html>
<html>
	<body>
		<?php
			$projid = $_POST["projid"];
			$userid = $_POST["userid"];
			$amount = $_POST["amount"];

			$dbconn = pg_connect("dbname=cs309 user=Daniel");
			$curr = " select curramount from project where projid=$1";
			pg_prepare($dbconn, "curr", $curr);
			$result = pg_execute($dbconn, "curr", array($projid));
			$row = pg_fetch_row($result);
			$newamount = $row[0] + $amount;

			$syncdb = "update project set curramount=$1 where projid=$2";
			pg_prepare($dbconn, "syncdb", $syncdb);
			$result = pg_execute($dbconn, "syncdb", array($newamount, $projid));

			$logdonation = "insert into funder (email, projid, datestamp, amount) values ($1, $2, $3, $4)";
			pg_prepare($dbconn, "logdonation", $logdonation);
			pg_execute($dbconn, "logdonation", array($userid, $projid, date("Y-m-d"), $amount));
			echo $newamount;
		?>
	</body>
</html>
