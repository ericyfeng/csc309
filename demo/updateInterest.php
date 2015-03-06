<!DOCTYPE html>
<html>
	<body>
		<?php
			$interestid = $_POST["interestid"];
			$email = $_POST["email"];
			$dbconn = pg_connect("dbname=cs309 user=Daniel");
			$insert = "insert into personalinterests values ($1, $2)";
			pg_prepare($dbconn, "insert", $insert);
			pg_execute($dbconn, "insert", array($email, $interestid));

			//straight up copy and pasted from profile.php initial display of interests
			$myinterest = "select description from personalinterests natural join interests where email=$1";
			pg_prepare($dbconn, "myinterest", $myinterest);
			$result = pg_execute($dbconn, "myinterest", array($email));
			echo "<ul>";
			while ($row = pg_fetch_row($result))
			{
				echo "<li>$row[0]</li>";
			}
			echo "</ul>";
			echo "<p> it AJAXed, add $interestid for $email</p>";
		?>
	</body>
</html>
