<!DOCTYPE html>
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
		
		$id = $_GET["p"];
		$email = $_GET["email"];
		$dbconn = pg_connect("dbname=cs309 user=Daniel");

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
							
						<!--Fakeish log out button-->
						<a href="index.html" class="navbar-btn btn btn-primary"">
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
		<div class="container-fluid">
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
					<td><?php echo "$row[2]"?></span></td>
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
					<td>$row[2]</td>
					<td>$row[3]</td>
				</tr>
					";
			}
		?>
			</table>
		</div>
	</body>
</html>











