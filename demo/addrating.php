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

			$dbconn = pg_connect("dbname=cs309 user=Daniel");

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
			$email = $_SESSION["email"];
			$projid = $_POST["projid"];
			$rating = $_POST["rating"];

			//to prevent uprating of a project by friends, you can only vote once per project
			$multirate = "select count(*) from rating where email=$1 and projid=$2";
			pg_prepare($dbconn, "multirate", $multirate);
			$result = pg_execute($dbconn, "multirate", array($email, $projid));
			$row = pg_fetch_row($result);
			if($row[0] != 0)
			{
				echo "You can only rate this project once!";
				exit();
			}
			
			//insert the new rating along with who is giving the rating into the database
			$newrating = "insert into rating (projid, email, rating) values ($1, $2, $3)";
			pg_prepare($dbconn, "newrating", $newrating);
			pg_execute($dbconn, "newrating", array($projid, $email, $rating));

			//recalculate the average rating with this new information
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
			
			//update the projects table with the new average rating
			$update = "update project set rating=$1 where projid=$2";
			pg_prepare($dbconn, "update", $update);
			pg_execute($dbconn, "update", array($recalced, $projid));
			echo $recalced;

		?>
	</body>
</html>
