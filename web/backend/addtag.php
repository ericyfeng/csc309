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
			include("checksession.php");

			//check for malicously direct fed data
			$pid = $_POST["pid"];
			$vemail = $_SESSION["email"];
			$verifyowner = "select count(*) from initiator where email=$1 and projid=$2";
			pg_prepare($dbconn, "verifyowner", $verifyowner);
			$vresult = pg_execute($dbconn, "verifyowner", array($vemail, $pid));
			$vrow = pg_fetch_row($vresult);
			if($vrow[0] != 1)
			{
				echo "You aren't authorized to add tags";
				exit();
			}

			//check for existing entries
			$commid = $_POST["commid"];
			$checkexist = "select count(*) from communityendorsement where commid=$1 and projid=$2";
			pg_prepare($dbconn, "checkexist", $checkexist);
			$result = pg_execute($dbconn, "checkexist", array($commid, $pid));
			$row = pg_fetch_row($result);
			if($row[0] == 0)
			{
				//add the tag to the community endorsement table
				$ins = "insert into communityendorsement values ($1, $2)";
				pg_prepare($dbconn, "ins", $ins);
				pg_execute($dbconn, "ins", array($commid, $pid));
			}
			//get the new list of tags associated with the project to return by ajax
			$currentTags = "select description from communityendorsement natural join community where projid=$1";
			pg_prepare($dbconn, "currentTags", $currentTags);
			$result3 = pg_execute($dbconn, "currentTags", array($pid));
			while($row3 = pg_fetch_row($result3))
			{
				echo "<span class=\"lead\"><i class=\"fa fa-tag\"></i> $row3[0] </span>";
			}

		?>
	</body>
</html>
