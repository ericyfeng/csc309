<!DOCTYPE html>
<?php
	session_start();
?>
<html>
	<body>
		<?php
			include("checksession.php");

			//setup variables for adding a comment
			$email = $_SESSION["email"];
			$projid = $_POST["pid"];
			$comment = $_POST["comment"];
			$fname = $_SESSION["fname"];
			$lname = $_SESSION["lname"];

			//insert the new comment
			$newcomment = "insert into comment (projid, email, comment) values ($1, $2, $3)";
			pg_prepare($dbconn, "newcomment", $newcomment);
			pg_execute($dbconn, "newcomment", array($projid, $email, $comment));
		?>
	</body>
</html>
