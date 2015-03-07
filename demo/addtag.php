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

			$dbconn = pg_connect("dbname=cs309 user=Daniel");

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

			//add the tag to the community endorsement table
			$pid = $_POST["pid"];
			$commid = $_POST["commid"];
			$ins = "insert into communityendorsement values ($1, $2)";
			pg_prepare($dbconn, "ins", $ins);
			pg_execute($dbconn, "ins", array($commid, $pid));
			
			//get the new list of tags associated with the project to return by ajax
			$currentTags = "select description from communityendorsement natural join community where projid=$1";
			pg_prepare($dbconn, "currentTags", $currentTags);
			$result3 = pg_execute($dbconn, "currentTags", array($pid));
			while($row3 = pg_fetch_row($result3))
			{
				echo "<li class=\"glyphicon glyphicon-tag btn btn-danger\"> $row3[0]</li> ";
			}

		?>
	</body>
</html>
