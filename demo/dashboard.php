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

		<title>Simple Dashboard</title>
	</head>

	<body>
		<?php
			//enable php debugging
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
			date_default_timezone_set('America/Toronto');
			$dbconn = pg_connect("dbname=cs309 user=Daniel");

			//check if session id is real or faked
			$sessid = $_GET["sessid"];
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
			
			if($dbdate > new DateTime())
			{
				//the session has not expired yet, get the user variables for printing
				$fname = $_SESSION["fname"];
				$lname = $_SESSION["lname"];
				$email = $_SESSION["email"];
			?>

				<!--Black nav bar-->
				<nav class="navbar navbar-inverse">
					<div class="container-fluid">
						<div class="navbar-header">
							<div class="navbar-brand"><?php echo $fname?> <?php echo $lname?>'s Scrap Funder</div>
						</div>

						<div class="navbar-nav navbar-right">
							<!--New project button-->
							<a href="newproject.php?sessid=<?php echo $sessid?>" class="navbar-btn btn btn-success"">
								<span class="glyphicon glyphicon-asterisk"></span> New Project
							</a>
							<!--My profile button-->
							<a href="profile.php?sessid=<?php echo $sessid?>" class="navbar-btn btn btn-primary"">
								<span class="glyphicon glyphicon-user"></span> My Profile
							</a>
							
							<!--Real deal log out button-->
							<a href="logout.php?sessid=<?php echo $sessid?>" class="navbar-btn btn btn-primary"">
								<span class="glyphicon glyphicon-off"></span> Log Out
							</a>
						</div>
					</div>
				</nav>
				
				<!--Guts of the webpage-->
				<div class="container-fluid">
					<h3>My Projects</h3>
					<table class="table table-striped">
						<thead>
						<tr>
							<th>Description</th>
							<th>Current Funding</th>
							<th>Target Funding</th>
						</tr>
						</thead>

					<!--PHP to fetch the user's projects 1 by 1 and creat the corresponding rows-->
					<?php
					$myproj = "select description, curramount, goalamount, projid from initiator natural join project where email=$1";
					pg_prepare($dbconn, "myproj", $myproj);
					$result = pg_execute($dbconn, "myproj", array($email));

					//create a new table entry for each of the user's projects
					while($row = pg_fetch_row($result))
					{
						$description=$row[0];
						$curramount=$row[1];
						$goalamount=$row[2];
						$projid=$row[3];
						echo "<tr>
								<td><a href=\"projhistory.php?p=$projid&sessid=$sessid\">$description</a></td>
								<td>\$$curramount</td>
								<td>\$$goalamount</td>
							</tr>";
					}?>


					</table>			
					<h3>Other Projects</h3>
					<table class="table table-striped">
						<thead>
						<tr>
							<th>Description</th>
							<th>End Date</th>
							<th>Location</th>
						</tr>
						</thead>
					<?php
					$others = "select distinct projid, description, enddate, location from initiator natural join project where (projid) not in (select projid from initiator where email=$1)";
					pg_prepare($dbconn, "others", $others);
					$result = pg_execute($dbconn, "others", array($email));

					while($row = pg_fetch_row($result))
					{?>
						<tr>
							<td><a href="overview.php?p=<?php echo $row[0]?>&sessid=<?php echo $sessid?>"> <?php echo $row[1]?></a><br></td>
							<td><?php echo $row[2]?></td>
							<td><?php echo $row[3]?></td>
						</tr>
					<?php
					}
					?>
					</table>
				</div>
			<?php
			}
			else
			{
				echo "Please log in again."; //give no clues as to why the login failed
			}
			?>

	</body>
</html>
