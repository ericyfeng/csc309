<!DOCTYPE html>
<?php
	session_start();
?>
<html>
	<body>
		<?php
			//standard error print and config
			
			include("checksession.php");
			$sessid = $_SESSION["sessid"];

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
