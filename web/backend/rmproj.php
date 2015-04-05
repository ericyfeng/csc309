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
			include("checksession.php");

			//check to make sure the user is an owner of the project
			$email = $_SESSION["email"];
			$pid = $_POST["pid"];
			$ckowner = "select count(*) from initiator where email=$1 and projid=$2";
			pg_prepare($dbconn, "ckowner", $ckowner);
			$result = pg_execute($dbconn, "ckowner", array($email, $pid));
			$row = pg_fetch_row($result);
			if (!($row[0] == 1 || $_SESSION["admin"] == 1))
			{
				echo "You are neither the owner or an administrator. You cannot delete this project.";
				exit();
			}

			//remove project information related entries in other tables will cascade
			$rmproj = "delete from project where projid=$1";
			pg_prepare($dbconn, "rmproj", $rmproj);
			$effno = pg_execute($dbconn, "rmproj", array($pid));
			
			//check to make sure the removal actually happened
			if($effno > 0)
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






