<!DOCTYPE html>
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
			error_reporting(E_ALL);
			ini_set('display_errors', 1);

			$dbconn = pg_connect("dbname=cs309 user=Daniel");

			$uname = $_POST["email"];
			$passwd = $_POST["passwd"];
			$verify = "select count(*) from users where email=$1 and password=$2";
			pg_prepare($dbconn, "preparelogin", $verify);
			$result = pg_execute($dbconn, "preparelogin", array($uname, $passwd));
			$row = pg_fetch_row($result);
			if($row[0])
			{
				$getname = "select fname, lname from users where email=$1";
				pg_prepare($dbconn, "getname", $getname);
				$result = pg_execute($dbconn, "getname", array($uname));
				$row = pg_fetch_row($result);
				$fname=$row[0];
				$lname=$row[1];
			?>

				<!--Black nav bar-->
				<nav class="navbar navbar-inverse">
					<div class="container-fluid">
						<div class="navbar-header">
							<div class="navbar-brand"><?php echo $fname?> <?php echo $lname?>'s Scrap Funder</div>
						</div>

						<div class="navbar-nav navbar-right">
							<!--New project button-->
							<a href="newproject.php?user=<?php echo $uname?>" class="navbar-btn btn btn-success"">
								<span class="glyphicon glyphicon-asterisk"></span> New Project
							</a>
							<!--My profile button-->
							<a href="profile.php?fname=<?php echo $fname?>&lname=<?php echo $lname?>&user=<?php echo $uname?>" class="navbar-btn btn btn-primary"">
								<span class="glyphicon glyphicon-user"></span> My Profile
							</a>
							
							<!--Fakeish log out button-->
							<a href="index.html" class="navbar-btn btn btn-primary"">
								<span class="glyphicon glyphicon-off"></span> Log Out
							</a>
						</div>
					</div>
				</nav>
				
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
					$result = pg_execute($dbconn, "myproj", array($uname));
					while($row = pg_fetch_row($result))
					{
						$description=$row[0];
						$curramount=$row[1];
						$goalamount=$row[2];
						$projid=$row[3];
						echo "<tr>
								<td><a href=\"projhistory.php?p=$projid&email=$uname\">$description</a></td>
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
					$result = pg_execute($dbconn, "others", array($uname));

					while($row = pg_fetch_row($result))
					{?>
						<tr>
							<td><a href="overview.php?p=<?php echo $row[0]?>&user=<?php echo $uname?>"> <?php echo $row[1]?></a><br></td>
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
				echo "invalid credentials";
			}
		?>

	</body>
</html>
