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

			//setup variables for adding a rating
			$rater = $_SESSION["email"];
			$ratee = $_POST["ratee"];
			$urating = $_POST["urating"];

			//to prevent uprating of a user by friends, you can only vote once per project
			$multirate = "select count(*) from userrating where rater=$1 and ratee=$2";
			pg_prepare($dbconn, "multirate", $multirate);
			$result = pg_execute($dbconn, "multirate", array($rater, $ratee));
			$row = pg_fetch_row($result);
			if($row[0] != 0)
			{
				echo "You can only rate this person once!";
				exit();
			}
			
			//insert the new rating along with who is giving the rating into the database
			$newrating = "insert into userrating (rater, ratee, urating) values ($1, $2, $3)";
			pg_prepare($dbconn, "newrating", $newrating);
			pg_execute($dbconn, "newrating", array($rater, $ratee, $urating));

			//recalculate the average rating with this new information
			$acc=0; //accumulator
			$count=0;
			$calculate = "select urating from userrating where ratee=$1";
			pg_prepare($dbconn, "calculate", $calculate);
			$result = pg_execute($dbconn, "calculate", array($ratee));
			while($row = pg_fetch_row($result))
			{
				$acc = $acc + $row[0];
				$count++;
			}
			$recalced = $acc/$count;
			
			//update the projects table with the new average rating
			$update = "update users set reputation=$1 where email=$2";
			pg_prepare($dbconn, "update", $update);
			pg_execute($dbconn, "update", array($recalced, $ratee));
			echo $recalced;

		?>
	</body>
</html>
