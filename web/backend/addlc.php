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
			$locid = $_POST["locid"];
			$comment = $_POST["comment"];

			//insert the new comment
			$newcomment = "insert into loccomment (locid, email, comment) values ($1, $2, $3)";
			pg_prepare($dbconn, "newcomment", $newcomment);
			pg_execute($dbconn, "newcomment", array($locid, $email, $comment));
		?>
	</body>
</html>
