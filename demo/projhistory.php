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

		<script>
			function addinit(id)
			{
				var email = document.getElementById("newinit").value;
				var ajax = new XMLHttpRequest();
				ajax.onreadystatechange = function ()
				{
					document.getElementById("status").innerHTML=ajax.responseText;
				}
				ajax.open("POST", "addinit.php", true);
				ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				ajax.send("id="+id+"&email="+email);
			}
		</script>

		<title>Simple History</title>
	</head>
	
	<body>
	<?php
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		date_default_timezone_set('America/Toronto');

		//establish all varibles received from GET and session information
		$id = $_GET["p"];
		$sessid = $_GET["sessid"];
		$fname = $_SESSION["fname"];
		$lname = $_SESSION["lname"];
		$email= $_SESSION["email"];
		$dbconn = pg_connect("dbname=cs309 user=Daniel");

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

		//with a valid unexpired session id#, now check if the user is an initiator of the project or not
		$isinit = "select count(*) from initiator where projid=$1 and email=$2";
		pg_prepare($dbconn, "isinit", $isinit);
		$result = pg_execute($dbconn, "isinit", array($id, $email));
		$row = pg_fetch_row($result);
		if($row[0] != 1)
		{
			echo "You are not an initiator of this project";
			exit();
		}

		//FINALL the session id is real, it isn't expired, and this person is an initiator of the project
		//	let's get on with the show
		$name = "select fname, lname from users where email=$1";
		pg_prepare($dbconn, "name", $name);
		$result = pg_execute($dbconn, "name", array($email));
		$row = pg_fetch_row($result);
		$fname = $row[0];
		$lname = $row[1];

		$summary = "select * from project where projid=$1";
		pg_prepare($dbconn, "summary", $summary);
		$result = pg_execute($dbconn, "summary", array($id));
		$row = pg_fetch_row($result);
		if($row[8]==0) $rating="No ratings yet";
		else $rating=$row[8];
	?>

		<!--Black nav bar-->
		<nav class="navbar navbar-inverse">
			<div class="container-fluid">
				<div class="navbar-header">
					<div class="navbar-brand"><?php echo $fname?> <?php echo $lname?>'s Scrap Funder</div>
					</div>

					<div class="navbar-nav navbar-right">
					
						<!--Add Another Owner button-->
						<a href="#" class="navbar-btn btn btn-success" data-toggle="modal" data-target="#addowner">
							<span class="glyphicon glyphicon-user"></span> +
						</a>

						<!--My profile button-->
						<a href="profile.php?fname=<?php echo $fname?>&lname=<?php echo $lname?>&user=<?php echo $uname?>" class="navbar-btn btn btn-primary"">
							<span class="glyphicon glyphicon-user"></span> My Profile
						</a>
							
						<!--Real functioning log out button-->
						<a href="logout.php?sessid=<?php echo $sessid?>" class="navbar-btn btn btn-primary"">
							<span class="glyphicon glyphicon-off"></span> Log Out
						</a>
						</div>
					</div>
				</nav>

		<!--Add owner popup-->
		<div class="modal fade" id="addowner" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
			<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4>Add Project Owner</h4>
				</div>
				<div class="modal-body">
					<input type="text" id="newinit"></input>
					<input type="button" class="btn btn-warning" onclick="addinit('<?php echo $id?>')" value="+">
				</div>
				</div>
			</div>
		</div>

		<!--Project stats-->
		<div class="container">
			<h3><b><?php echo "$row[5]"?></b></h3>
			<table class="table table-striped table-bordered">
				<tr>
					<td><b>Starting Date:</b></td>
					<td><?php echo "$row[3]"?></td>
				</tr>
				<tr>
					<td><b>End Date:</b></td>
					<td><?php echo "$row[4]"?></td>
				</tr>
				<tr>
					<td><b>Location:</b></td>
					<td><?php echo "$row[6]"?></td>
				</tr>
				<tr>
					<td><b>Current Fudning:</b></td>
					<td>$<?php echo "$row[2]"?></span></td>
				</tr>
				<tr>
					<td><b>Target Fuding:</b></td>
					<td>$<?php echo "$row[1]"?></td>
				</tr>
				<tr>
					<td><b>Community Rating:</b></td>
					<td><?php echo "$rating"?></td>
				</tr>
			</table>

			<p id="status" style="color:blue;font-size:70%"></p>

			<!--Donation History-->
			<h3><b>Donation History</b></h3>
			<table class="table table-striped table-bordered">
				<tr>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Amount</th>
					<th>Date</th>
				</tr>
		<?php
			$history = "select fname, lname, amount, datestamp from funder natural join users where projid=$1 order by datestamp desc";
			pg_prepare($dbconn, "history", $history);
			$result = pg_execute($dbconn, "history", array($id));
			while ($row = pg_fetch_row($result))
			{
				echo "
				<tr>
					<td>$row[0]</td>
					<td>$row[1]</td>
					<td>\$$row[2]</td>
					<td>$row[3]</td>
				</tr>
					";
			}
		?>
			</table>
		</div>
	</body>
</html>











