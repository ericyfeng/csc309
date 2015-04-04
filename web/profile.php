<!DOCTYPE html>
<?php
	include("backend/checksession.php");
	$fname = $_SESSION["fname"];
	$lname = $_SESSION["lname"];
	$email = $_SESSION["email"];
?>

<html>
	<head>
		<title>Simple Profile Management</title>

		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

		<!-- jQuery library -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

		<!-- Latest compiled JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

		<script>
		//automatically add the new interest/community to the user's list
		function addInterest (buttonid, commid, sessid)
		{
			document.getElementById(buttonid).style.display="none";
			updateInterest = new XMLHttpRequest();
			updateInterest.onreadystatechange= function ()
			{
				document.getElementById("interests").innerHTML=updateInterest.responseText;
			}
			updateInterest.open("POST", "updateInterest.php", true);
			updateInterest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			updateInterest.send("commid="+commid+"&sessid="+sessid);
		}
		</script>
	</head>

	<body>
		<?php
			//get current list of interested communities
			$myinterest = "select description from personalinterests natural join community where email=$1";
			pg_prepare($dbconn, "myinterest", $myinterest);
			$result = pg_execute($dbconn, "myinterest", array($email));

			$myrating = "select reputation from users where email=$1";
			pg_prepare($dbconn, "myrating", $myrating);
			$result2 = pg_execute($dbconn, "myrating", array($email));
			$row2 = pg_fetch_row($result2);
			$myratingvalue = $row2[0];
			if($myratingvalue == 0)
			{
				$myratingvalue = "You have never been rated";
			}
		?>

		<!--Black nav bar-->
		<nav class="navbar navbar-inverse">
			<div class="container-fluid">
				<div class="navbar-header">
					<div class="navbar-brand"><?php echo $fname?> <?php echo $lname?>'s Scrap Funder</div>
					</div>

					<div class="navbar-nav navbar-right">
							
						<!--real log out button-->
						<a href="logout.php?sessid=<?php echo $sessid?>" class="navbar-btn btn btn-primary"">
							<span class="glyphicon glyphicon-off"></span> Log Out
						</a>
						</div>
					</div>
				</nav>

		<div class="container">

			<!--List of the current interestsed communities-->
			<h1><?php echo"$fname $lname"?> 's Profile</h1>
			<h3>My Communities' Interests</h3>
				<div id="interests">
					<ul>
						<?php
						while ($row = pg_fetch_row($result))
						{
							echo "<li>$row[0]</li>";
						}?>
					</ul>
				</div>
			<h3>My Community Rating: <?php echo $myratingvalue?></h3>
			<!--List of community interests the user hasen't chosen-->
			<h3>Add an interest:</h3><br>
			<?php
			$unchosen = "select * from community where (commid) not in (select commid from personalinterests where email=$1)";
			pg_prepare($dbconn, "unchosen", $unchosen);
			$result = pg_execute($dbconn, "unchosen", array($email));
			
			while($row = pg_fetch_row($result))
			{
				$id = $row[0];
				echo "<button class=\"btn btn-primary\" id=\"button$id\" onclick=\"addInterest('button$id', '$id', '$sessid')\"> \n $row[1] </button><br>\n";
			}
			?>
		</div>
	</body>
</html>
