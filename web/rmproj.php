<!DOCTYPE html>
<?php
	session_start();
?>

<html>
	<head>
		<title>Add Tag</title>
	</head>

	<body>
		<?php
			//setup error print and config
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
			date_default_timezone_set('America/Toronto');

			$dbconn = pg_connect("dbname=d8dt3b69jeev6n host=ec2-50-19-249-214.compute-1.amazonaws.com port=5432 user=fhntmyljqrdquf password=vgJO4ZQS8Mi7OceXpIzk_dYL0- sslmode=require");

			//check if session id is real or faked
			$sessid = $_POST["sessid"];
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

			//check to make sure the user is an owner of the project
			$email = $_SESSION["email"];
			$pid = $_POST["pid"];
			$pid = substr($pid, 2);
			$ckowner = "select count(*) from initiator where email=$1 and projid=$2";
			pg_prepare($dbconn, "ckowner", $ckowner);
			$result = pg_execute($dbconn, "ckowner", array($email, $pid));
			$row = pg_fetch_row($result);
			if (!($row[0] == 1 || $_SESSION["admin"] == 1))
			{
				echo "You are neither the owner or an administrator. You cannot delete this project.";
				exit();
			}

			//remove project owners
			$rminit = "delete from initiator where projid=$1";
			pg_prepare($dbconn, "rminit", $rminit);
			$effno = pg_execute($dbconn, "rminit", array($pid));

			//remove project ratings
			$rmrating = "delete from rating where projid=$1";
			pg_prepare($dbconn, "rmrating", $rmrating);
			$effno2 = pg_execute($dbconn, "rmrating", array($pid));

			//remove project donation history
			$rmfunding = "delete from funder where projid=$1";
			pg_prepare($dbconn, "rmfunding", $rmfunding);
			$effno3 = pg_execute($dbconn, "rmfunding", array($pid));

			//remove project tags
			$rmcomm = "delete from communityendorsement where projid=$1";
			pg_prepare($dbconn, "rmcomm", $rmcomm);
			$effno4 = pg_execute($dbconn, "rmcomm", array($pid));

			//finally remove project information now that no foreign keys are referencing it
			$rmproj = "delete from project where projid=$1";
			pg_prepare($dbconn, "rmproj", $rmproj);
			$effno5 = pg_execute($dbconn, "rmproj", array($pid));
			
			//check to make sure the removal actually happened
			if($effno > 0 && $effno2 > 0 && $effno3 > 0 && $effno4 > 0 && $effno5 > 0)
			{
				echo "<span style=\"color:green\">All project information wiped from database</span>";
			}
			else
			{
				echo "<span style=\"color:red\">Something went wrong</span>";
			}
		?>
	</body>
</html>






