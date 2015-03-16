<!DOCTYPE html>
<?php
	session_start();
?>

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
			date_default_timezone_set("America/Toronto");
			$dbconn = pg_connect("dbname=d8dt3b69jeev6n host=ec2-50-19-249-214.compute-1.amazonaws.com port=5432 user=fhntmyljqrdquf password=vgJO4ZQS8Mi7OceXpIzk_dYL0- sslmode=require");

			$email = $_SESSION["email"]; //email is stored in the php session
			$sessid = $_GET["sessid"];
	
			//check if session id is real or faked
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
						$avg = "select avg(amount) from funder where email=$1";
						pg_prepare($dbconn, "avg", $avg);
						$result = pg_execute($dbconn, "avg", array($semail));
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
							$allproj = "select count(*) from project natural join initiator where email=$1";
							pg_prepare($dbconn, "allproj", $allproj);
							$result = pg_execute($dbconn, "allproj", array($semail));
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
							$currproj = "select count(*) from project natural join initiator where email=$1 and enddate > current_timestamp";
							pg_prepare($dbconn, "currproj", $currproj);
							$result = pg_execute($dbconn, "currproj", array($semail));
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
							$donecurr = "select count(*) from project natural join initiator where  email=$1 and curramount >= goalamount and enddate <= current_timestamp;";
							pg_prepare($dbconn, "donecurr", $donecurr);
							$result = pg_execute($dbconn, "donecurr", array($semail));
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
							$doneexp = "select count(*) from project natural join initiator where  email=$1 and curramount >= goalamount and enddate > current_timestamp;";
							pg_prepare($dbconn, "doneexp", $doneexp);
							$result = pg_execute($dbconn, "doneexp", array($semail));
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
			<?php }?>
		</div>
	<body>
</html>
