<!DOCTYPE html>
<html>
	<head>
		<title>Simple Rating System</title>
	</head>

	<body>
		<?php
			error_reporting(E_ALL);
			ini_set('display_errors', 1);

			$dbconn = pg_connect("dbname=cs309 user=Daniel");
			$email = $_POST["userid"];
			$projid = $_POST["projid"];
			$rating = $_POST["rating"];

			$multirate = "select count(*) from rating where email=$1 and projid=$2";
			pg_prepare($dbconn, "multirate", $multirate);
			$result = pg_execute($dbconn, "multirate", array($email, $projid));
			$row = pg_fetch_row($result);
			if($row[0]==0)
			{
				$newrating = "insert into rating (projid, email, rating) values ($1, $2, $3)";
				pg_prepare($dbconn, "newrating", $newrating);
				pg_execute($dbconn, "newrating", array($projid, $email, $rating));

				$acc=0; //accumulator
				$count=0;
				$calculate = "select rating from rating where projid=$1";
				pg_prepare($dbconn, "calculate", $calculate);
				$result = pg_execute($dbconn, "calculate", array($projid));
				while($row = pg_fetch_row($result))
				{
					$acc = $acc + $row[0];
					$count++;
				}
				$recalced = $acc/$count;
				
				$update = "update project set rating=$1 where projid=$2";
				pg_prepare($dbconn, "update", $update);
				pg_execute($dbconn, "update", array($recalced, $projid));
				echo $recalced;
			}
			else
			{
				echo "You can only rate this project once!";
			}
		?>
	</body>
</html>
