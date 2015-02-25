<!DOCTYPE html>
<html>
	<head>
		<title>Simple Profile Management</title>
		<script>
			function addInterest (buttonid, interest, email)
			{
				document.getElementById(buttonid).style.visibility="hidden";
				updateInterest = new XMLHttpRequest();
				updateInterest.onreadystatechange= function ()
				{
					document.getElementById("interests").innerHTML=updateInterest.responseText;
				}
				updateInterest.open("POST", "updateInterest.php", true);
				updateInterest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				updateInterest.send("interestid="+interest+"&email="+email);
			}
			function verbose(v1, v2)
			{
					document.getElementById("interests").innerHTML=v1+" "+v2;
			}
		</script>
	</head>

	<body>
		<?php
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
			$fname = $_POST["fname"];
			$lname = $_POST["lname"];
			$email = $_POST["user"];
			$dbconn = pg_connect("dbname=cs309 user=Daniel");
			echo "<h1>$fname $lname's Profile</h1>\n";

			echo "\t\t<h3>My Interests</h3>\n";
			$myinterest = "select description from personalinterests natural join interests where email=$1";
			pg_prepare($dbconn, "myinterest", $myinterest);
			$result = pg_execute($dbconn, "myinterest", array($email));
			echo "\t\t\t<div id=\"interests\">\n";
			echo "<ul>\n";
			while ($row = pg_fetch_row($result))
			{
				echo "<li>$row[0]</li>\n";
			}
			echo "</ul>\n";
			echo "</div>\n";
			
			echo "<h3>Add an interest:</h3><br>\n";
			$unchosen = "select * from interests where (interestid) not in (select interestid from personalinterests where email=$1)";
			pg_prepare($dbconn, "unchosen", $unchosen);
			$result = pg_execute($dbconn, "unchosen", array($email));
			
			while($row = pg_fetch_row($result))
			{
				$id = $row[0];
				echo "<button id=\"button$id\" onclick=\"addInterest('button$id', '$id', '$email')\"> \n $row[1] </button><br>\n";

			}
		?>
	</body>
</html>
