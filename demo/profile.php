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
			$fname = $_GET["fname"];
			$lname = $_GET["lname"];
			$email = $_GET["user"];
			$dbconn = pg_connect("dbname=cs309 user=Daniel");
			$myinterest = "select description from personalinterests natural join interests where email=$1";
			pg_prepare($dbconn, "myinterest", $myinterest);
			$result = pg_execute($dbconn, "myinterest", array($email));
		?>

			<h1><?php echo"$fname $lname"?> 's Profile</h1>
			<h3>My Interests</h3>
				<div id="interests">
					<ul>

						<?php
						while ($row = pg_fetch_row($result))
						{
							echo "<li>$row[0]</li>";
						}?>
					</ul>
				</div>
			
			<h3>Add an interest:</h3><br>
				<?php
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
