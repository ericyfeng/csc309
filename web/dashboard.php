<!DOCTYPE html>
<?php
	include("backend/checksession.php");
		//the session has not expired yet, get the user variables for printing
	$fname = $_SESSION["fname"];
	$lname = $_SESSION["lname"];
?>

<html>
	<head>
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

		<!-- jQuery library -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

		<!-- Latest compiled JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

		<title>Simple Dashboard</title>

		<script>
		//performs the filtering of other peoples's projects when you click the red filter by tag button
		function filtertags(comm_requested)
		{

			var blacklist = document.getElementsByClassName("commgeneric");
			var j;
			for(j=0; j<blacklist.length; j++)
			{
				blacklist[j].style.display = "none";
			}
			
			var fixlist = document.getElementsByClassName(comm_requested);
			for(j=0; j<fixlist.length; j++)
			{
				fixlist[j].style.display = "table-row";
			}
		}

		function rm(pid, sessid)
		{
			var ajax = new XMLHttpRequest();
			ajax.onreadystatechange = function ()
			{
				document.getElementById(pid).innerHTML=ajax.responseText;
			}
			ajax.open("POST", "rmproj.php", true);
			ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			ajax.send("sessid="+sessid+"&pid="+pid);
		}
		</script>
	</head>

	<body>

		<!--Black nav bar-->
		<nav class="navbar navbar-inverse">
			<div class="container-fluid">
				<div class="navbar-header">
					<div class="navbar-brand"><?php echo $fname?> <?php echo $lname?>'s Scrap Funder</div>
				</div>

				<div class="navbar-nav navbar-right">
					<?php
					if($_SESSION["admin"] == 1)
					{
					?>
					<!--Admin button-->
					<a href="admin2.php?sessid=<?php echo $sessid?>" class="navbar-btn btn btn-danger">
						<span class="glyphicon glyphicon-wrench"></span> Administration
					</a>
					<?php }?>
					<!--New project button-->
					<a href="newproject.php" class="navbar-btn btn btn-success">
						<span class="glyphicon glyphicon-asterisk"></span> New Project
					</a>
					<!--My profile button-->
					<a href="profile.php" class="navbar-btn btn btn-primary">
						<span class="glyphicon glyphicon-user"></span> My Profile
					</a>
							
					<!--Real deal log out button-->
					<a href="logout.php" class="navbar-btn btn btn-primary">
						<span class="glyphicon glyphicon-off"></span> Log Out
					</a>
				</div>
			</div>
		</nav>
				
		<div class="container-fluid">

			<!--The left column for filtering projects by their community endorsement-->
			<div class="row">
				<div class="col-sm-2">
					<br><br>
						<button type="button" class= "btn btn-danger glyphicon glyphicon-tag" onclick="filtertags('commgeneric')"> All</button><br>
					<?php
						//get a list of all communities and make a checkbox for each one with the appropriate attributes
						$lscomm = "select * from community";
						pg_prepare($dbconn, "lscomm", $lscomm);
						$result4 = pg_execute($dbconn, "lscomm", array());
						while($row4 = pg_fetch_row($result4))
						{
							echo "<button type=\"button\" class=\"btn btn-danger glyphicon glyphicon-tag\" onclick=\"filtertags('comm$row4[0]')\">
								$row4[1]
								</button>";
						}
					?>

				</div>

				<!--The main right column for seeing your own projects as well as other peoples's projects-->
				<div class="col-sm-9">
					<!--Display the currently logged in user's projects-->
					<h3>My Projects</h3>
					<table class="table table-striped">
						<thead>
							<tr>
								<th>Description</th>
								<th>Current Funding</th>
								<th>Target Funding</th>
								<th>Cancel</th>
							</tr>
						</thead>

					<!--PHP to fetch the user's projects 1 by 1 and create the corresponding rows-->
					<?php
					$myproj = "select description, curramount, goalamount, projid, email from initiator natural join project where email=(select email from session where sessionid=$1)";
					pg_prepare($dbconn, "myproj", $myproj);
					$result = pg_execute($dbconn, "myproj", array($sessid));

					//create a new table entry for each of the user's projects
					while($row = pg_fetch_row($result))
					{
						$description = $row[0];
						$curramount = $row[1];
						$goalamount = $row[2];
						$projid = $row[3];
						echo "<tr id=\"p_$row[3]\">
								<td><a href=\"projhistory.php?p=$projid&sessid=$sessid\">$description</a></td>
								<td>\$$curramount</td>
								<td>\$$goalamount</td>
								<td><button class=\"btn btn-danger glyphicon glyphicon-remove\" onclick=\"rm('p_$row[3]', '$sessid')\"></button></td>
							</tr>";
					}
					$email = $row[4];
					?>

					<!--Display other peoples's projects-->
					</table>			
					<h3>Other Projects</h3>
					<table class="table table-striped">
						<thead>
							<tr>
								<th>Description</th>
								<th>End Date</th>
								<th>Location</th>
								<?php
								if($_SESSION["admin"] == 1)
								{
									echo"<th>Remove</th>";
								}
								?>
							</tr>
						</thead>

					<?php
					$others = "select distinct projid, description, enddate, location from initiator natural join project where (projid) not in (select projid from initiator where email=$1)";
					pg_prepare($dbconn, "others", $others);
					$result = pg_execute($dbconn, "others", array($email));
					$iteration = 0; //needed to create unique variable names for the while loop of prepared statements
					$taglist = "taglist"; //also needed for unique variable names

					while($row = pg_fetch_row($result))
					{
						/**
						  * Each "other person's" project is displayed as a table row.
						  * Each row's class attribute has the project's corresponding commids associated with it
						  * 
						  * This makes it easy to filter out projects you don't want to see by blacklisting it if
						  * it has of class "commid i don't wanna see"
						  *
						  * By the same logic, it also makes it easy to restore hidden projects
						  *
						  */
						//create a unique prepared statement name for each "other person's project"
						${$taglist.$iteration} = "select commid from communityendorsement where projid=$1";
						pg_prepare($dbconn, "taglist".$iteration, ${$taglist.$iteration});
						$result2 = pg_execute($dbconn, "taglist".$iteration, array($row[0]));
						$tagstring="";

						while($row2 = pg_fetch_row($result2))
						{//build the list of commid
							$tagstring = $tagstring . "comm" . $row2[0] . " ";
						}
						$iteration++;
						$tagstring = $tagstring . " commgeneric";
					?>
						<tr class="<?php echo $tagstring?>" id="p_<?php echo $row[0]?>">
							<td><a href="overview.php?p=<?php echo $row[0]?>&sessid=<?php echo $sessid?>"> <?php echo $row[1]?></a><br></td>
							<td><?php echo $row[2]?></td>
							<td><?php echo $row[3]?></td>
							<?php
							if($_SESSION["admin"] == 1)
							{
								echo "<td><button class=\"btn btn-danger glyphicon glyphicon-remove\" onclick=\"rm('p_$row[0]', '$sessid')\"></button></td>";
							} ?>
						</tr>
					<?php
					}
					?>
					</table>
				</div>
			</div>
		</div>
	</body>
</html>
