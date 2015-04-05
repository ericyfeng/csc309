<!DOCTYPE html>
<html>
	<head>
		<?php
			include("template/head.php");
			include("backend/checksession.php");
			$useremail = "select email from session where sessionid=$1";
	  		pg_prepare($dbconn, "useremail", $useremail);
	  		$result = pg_execute($dbconn, "useremail", array($sessid));
	  		$row = pg_fetch_row($result);
	  		$email = $row[0];		
			$fname = $_SESSION["fname"];
			$lname = $_SESSION["lname"];
		?>		
			<title>Profile</title>
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
			include("template/loginnav.php");
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
		<div class="container">	
			<div class="row mt centered ">
				<div class="col-lg-4 col-lg-offset-4">
					<h3><?php echo $fname . " " . $lname ?></h3>
					<hr>
				</div>
			</div><!-- /row -->

			<div class="row">

				<h3>My Communities</h3>
					<div id="interests">
						<ul>
							<?php
							while ($row = pg_fetch_row($result))
							{
								echo "<li>$row[0]</li>";
							}?>
						</ul>
					</div>
				<h3>My Rating: <?php echo $myratingvalue?></h3>
				<!--List of community interests the user hasen't chosen-->
				<h3>Join an Community</h3><br>
				<?php
				$unchosen = "select * from community where (commid) not in (select commid from personalinterests where email=$1)";
				pg_prepare($dbconn, "unchosen", $unchosen);
				$result = pg_execute($dbconn, "unchosen", array($email));
				
				while($row = pg_fetch_row($result))
				{
					$id = $row[0];
					echo "<button class=\"btn-theme\" id=\"button$id\" onclick=\"addInterest('button$id', '$id', '$sessid')\"> \n $row[1] </button><br>\n";
				}
				?>
			</div>


		</div>
	</body>


</html>