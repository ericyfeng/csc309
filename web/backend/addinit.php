<!DOCTYPE html>
<?php
	session_start();
?>

<html>
	<head>
		<title>Add Owner Backend</title>
	</head>

	<body>
		<?php
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
				echo "You aren't authorized to add owners";
				exit();
			}

			//valid session id# that isn't expired, proceed to add the person as an initiator
			$email = $_POST["email"];
			$ins = "insert into initiator values ($1, $2)";
			pg_prepare($dbconn, "ins", $ins);
			pg_execute($dbconn, "ins", array($pid, $email));
			
			echo "Successfully added";
		?>
	</body>
</html>
