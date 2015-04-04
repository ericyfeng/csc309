<!DOCTYPE html>
<html>
	<head>
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

		<!-- jQuery library -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

		<!-- Latest compiled JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

		<title>Administration</title>
	</head>

	<body>
		<?php
			include("backend/checksession.php");

			//prevent Andy's style of cheating the system
			if($_SESSION["admin"] == 0)
			{
				echo "You don't have administration rights";
				exit();
			}
			
			$fname = $_SESSION["fname"];
			$lname = $_SESSION["lname"];
		?>
		<!--Black nav bar-->
		<nav class="navbar navbar-inverse">
			<div class="container-fluid">
				<div class="navbar-header">
					<div class="navbar-brand"><?php echo $fname?> <?php echo $lname?>'s Scrap Funder</div>
				</div>

				<div class="navbar-nav navbar-right">
					<a href="profile.php?sessid=<?php echo $sessid?>" class="navbar-btn btn btn-primary">
						<span class="glyphicon glyphicon-user"></span> My Profile
					</a>
							
					<!--Real deal log out button-->
					<a href="logout.php?sessid=<?php echo $sessid?>" class="navbar-btn btn btn-primary">
						<span class="glyphicon glyphicon-off"></span> Log Out
					</a>
				</div>
			</div>
		</nav>

		<div class="container">
			<h3>Scrap Funder Statistics</h3>
			<table class="table table-striped table-bordered">
				<tr>
					<td>Registered Users</td>
					<td>
					<?php
						$reg = "select count(*) from users";
						pg_prepare($dbconn, "reg", $reg);
						$result = pg_execute($dbconn, "reg", array());
						$row = pg_fetch_row($result);
						echo $row[0];
					?>
					</td>
				</tr>
				<tr>
					<td>Total Projects</td>
					<td>
					<?php
						$projects = "select count(*) from project";
						pg_prepare($dbconn, "projects", $projects);
						$result = pg_execute($dbconn, "projects", array());
						$row = pg_fetch_row($result);
						echo $row[0];
					?>
					</td>
				</tr>
				<tr>
					<td>Total Money Raised</td>
					<td>
					<?php
						$money = "select sum(amount) from funder";
						pg_prepare($dbconn, "money", $money);
						$result = pg_execute($dbconn, "money", array());
						$row = pg_fetch_row($result);
						echo "$" . $row[0];
					?>
					</td>
				</tr>
			</table>
	
			<?php
			$users = "select email, fname, lname from users";
			pg_prepare($dbconn, "users", $users);
			$list = pg_execute($dbconn, "users", array());
			
			//variables to setup unique query names			
			$avg = "avg";
			$allproj = "allproj";
			$currproj = "currproj";
			$donecurr = "donecurr";
			$doneexp = "doneexp";
			$i = 0;
			while($ustat = pg_fetch_row($list))
			{
				$sfname = $ustat[1];
				$slname = $ustat[2];
				$semail = $ustat[0];
			?>
				<h4><?php echo $sfname?> <?php echo $slname?>'s Information (<?php echo $semail?>)</h4>
				<table class="table table-bordered table-striped">
					<tr>
						<td><?php echo $sfname?>'s Average Donation</td>
					<td>
					<?php
						${$avg.$i} = "select avg(amount) from funder where email=$1";
						pg_prepare($dbconn, "avg".$i, ${$avg.$i});
						$result = pg_execute($dbconn, "avg".$i, array($semail));
						$row = pg_fetch_row($result);
						if($row[0] == 0.0)
						{
							echo $sfname . " has never donated";
						}
						else
						{
							echo "$" . $row[0];
						}
					?>
					</td>
					</tr>
					<tr>
						<td><b>Total Lifetime Projects</b></td>
						<td>
						<?php
							${$allproj.$i} = "select count(*) from project natural join initiator where email=$1";
							pg_prepare($dbconn, "allproj".$i, ${$allproj.$i});
							$result = pg_execute($dbconn, "allproj".$i, array($semail));
							$row = pg_fetch_row($result);
							$stotal = $row[0];
							if($stotal > 0)
							{
								echo $stotal;
							}
							else
							{
								echo $sfname . " has no projects";
							}
						?>
						</td>
					</tr>
					<tr>
						<td>Ongoing Projects</td>
						<td>
						<?php
							${$currproj.$i} = "select count(*) from project natural join initiator where email=$1 and enddate > current_timestamp";
							pg_prepare($dbconn, "currproj".$i, ${$currproj.$i});
							$result = pg_execute($dbconn, "currproj".$i, array($semail));
							$row = pg_fetch_row($result);
							$songoing = $row[0];
							if($songoing > 0)
							{
								echo $songoing;
							}
							else
							{
								if($stotal == 0) {echo $sfname . " has no projects";}
								else {echo $sfname . "'s projects have all expired";}
							}
						?>
						</td>
					</tr>
					<tr>
						<td>Expired Projects</td>
						<td>
						<?php
							$s_expired = $stotal - $songoing;
							if($s_expired > 0)
							{
								echo $s_expired;
							}
							else
							{
								if($stotal == 0) {echo $sfname . " has no projects";}
								else {echo $sfname . "'s projects are all ongoing";}
							}
						?>
						</td>
					</tr>
					<tr>
						<td>Completed But Still Ongoing Projects</td>
						<td>
						<?php
							${$donecurr.$i} = "select count(*) from project natural join initiator where  email=$1 and curramount >= goalamount and enddate <= current_timestamp;";
							pg_prepare($dbconn, "donecurr".$i, ${$donecurr.$i});
							$result = pg_execute($dbconn, "donecurr".$i, array($semail));
							$row = pg_fetch_row($result);
							if($row[0] > 0)
							{
								echo $row[0];
							}
							else
							{
								if($stotal == 0) {echo $sfname . " has no projects";}
								else {echo $sfname . "'s projects are still ongoing";}
							}
						?>
						</td>
					</tr>
					<tr>
						<td>Completed But Expired Projects</td>
						<td>
						<?php
							${$doneexp.$i} = "select count(*) from project natural join initiator where  email=$1 and curramount >= goalamount and enddate > current_timestamp;";
							pg_prepare($dbconn, "doneexp".$i, ${$doneexp.$i});
							$result = pg_execute($dbconn, "doneexp".$i, array($semail));
							$row = pg_fetch_row($result);
							if($row[0] > 0)
							{
								echo $row[0];
							}
							else
							{
								if($stotal == 0) {echo $sfname . " has no projects";}
								else {echo $sfname . "'s expired projects never completed";}
							}
						?>
						</td>
					</tr>
				</table>
			<?php 
				$i++;
				}?>
		</div>
	<body>
</html>
