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
			$commid = $_POST["commid"];
			$comment = $_POST["comment"];

			//insert the new comment
			$newcomment = "insert into commcomment (commid, email, comment) values ($1, $2, $3)";
			pg_prepare($dbconn, "newcomment", $newcomment);
			pg_execute($dbconn, "newcomment", array($commid, $email, $comment));
		?>
	</body>
</html>
