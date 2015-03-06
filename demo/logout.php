<!DOCTYPE html>
<?php
	session_start();
?>
<html>
	<body>
	<?php
		date_default_timezone_set("America/Toronto");
		error_reporting(E_ALL);
		ini_set('display_errors', 1);

		$dbconn = pg_connect("dbname=cs309 user=Daniel");
		$sessid = $_GET["sessid"];
		
		$killsess = "update session set expiration=(select localtimestamp) where sessionid=$1";
		pg_prepare($dbconn, "killsess", $killsess);
		pg_execute($dbconn, "killsess", array($sessid));
		session_unset();
		session_destroy();

		header("Location: index.html");
		exit();
	?>
	</body>
</html>
