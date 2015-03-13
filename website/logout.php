<!DOCTYPE html>
<?php
	session_start();
?>
<html>
	<body>
	<?php
		//setup debugging and default config
		date_default_timezone_set("America/Toronto");
		error_reporting(E_ALL);
		ini_set('display_errors', 1);

		$dbconn = pg_connect("dbname=cs309 user=Daniel");
		$sessid = $_GET["sessid"];
		
		//prematurely expire the session random key in the database
		$killsess = "update session set expiration=(select localtimestamp) where sessionid=$1";
		pg_prepare($dbconn, "killsess", $killsess);
		pg_execute($dbconn, "killsess", array($sessid));

		//remove php session variables
		session_unset();
		session_destroy();

		//go back to the home page after it's done
		header("Location: index.html");
		exit();
	?>
	</body>
</html>
