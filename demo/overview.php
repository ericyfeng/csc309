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
			function donate(userid, projid)
			{
				document.getElementById("antiocd").innerHTML="**to prevent OCD donations, the donation button has been disabled."
				document.getElementById("donate").disabled = true;
				var newAmount = new XMLHttpRequest();
				newAmount.onreadystatechange= function ()
				{
					document.getElementById("current").innerHTML=newAmount.responseText;
				}
				newAmount.open("POST", "addmoney.php", true);
				newAmount.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				newAmount.send("userid="+userid+"&projid="+projid+"&amount="+document.getElementById("donation").value);
			}

			function rate(projid, userid)
			{
				var ratingGroup = document.getElementsByName("rating");
				var rating;
				for(var i=0; i < 5; i++)
				{
					if(ratingGroup[i].checked)
					{
						rating=i+1; //ratings start at 1, button counting starts at 0
						break;
					}
				}
				var ajax = new XMLHttpRequest();
				ajax.onreadystatechange = function ()
				{
					document.getElementById("liverating").innerHTML=ajax.responseText;
				}
				ajax.open("POST", "addrating.php", true);
				ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				ajax.send("userid="+userid+"&projid="+projid+"&rating="+rating);
			}
		</script>
		<title>Simple Project Overview</title>
	</head>

	<body>
	<?php
		error_reporting(E_ALL);
		ini_set('display_errors', 1);

		$id = $_GET["p"];
		$uname = $_GET["user"];
		$dbconn = pg_connect("dbname=cs309 user=Daniel");

		$name = "select fname, lname from users where email=$1";
		pg_prepare($dbconn, "name", $name);
		$result = pg_execute($dbconn, "name", array($uname));
		$row = pg_fetch_row($result);
		$fname = $row[0];
		$lname = $row[1];

		$summary = "select * from project where projid=$1";
		pg_prepare($dbconn, "summary", $summary);
		$result = pg_execute($dbconn, "summary", array($id));
		$row = pg_fetch_row($result);
		$rating = $row[8];
	?>

		<!--Black nav bar-->
		<nav class="navbar navbar-inverse">
			<div class="container-fluid">
				<div class="navbar-header">
					<div class="navbar-brand"><?php echo $fname?> <?php echo $lname?>'s Scrap Funder</div>
					</div>

					<div class="navbar-nav navbar-right">

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

		<div class="container">

			<!--Project summary information-->
			<div class="row">
			<h1>Project Summary</h1>
			<h3> <?php echo "$row[5]"?> </h3>
			<table class="table table-bordered table-striped">
				<tr><td><b>Starting Date:</b></td><td><?php echo "$row[3]"?></td></tr>
				<tr><td><b>End Date:</b></td><td><?php echo "$row[4]"?></td></tr>
				<tr><td><b>Location:</b></td><td><?php echo "$row[6]"?></td></tr>
				<tr><td><b>Current Fudning:</b></td><td><span id="current">$<?php echo "$row[2]"?></span></td></tr>
				<tr><td><b>Target Fuding:</b></td><td>$<?php echo "$row[1]"?></td></tr>
			</table>
			</div>

			<!--Project initiator information-->
			<div class="row">
			<h3>Project Initiators</h3>
			<ul>
				<?php
					$inits = "select fname, lname from initiator natural join users where projid=$1";
					pg_prepare($dbconn, "inits", $inits);
					$result = pg_execute($dbconn, "inits", array($id));
					while ($row = pg_fetch_row($result))
					{?>
						<li><?php echo"$row[0] $row[1]"?></li>
					<?php
					}
				?>
			</ul>
			</div>

			<div class="row">
			<h3>Rate this project</h3>
				<p><b>Current Rating:</b> <span id="liverating"><?php echo $rating?></span></p>
				<input type="radio" name="rating" value="1"> 1
				<input type="radio" name="rating" value="2"> 2
				<input type="radio" name="rating" value="3"> 3
				<input type="radio" name="rating" value="4"> 4
				<input type="radio" name="rating" value="5"> 5
				<button type="button" class="btn btn-success" id="rate" onclick="rate('<?php echo $id?>', '<?php echo $uname?>')">Rate
			</div>

			<div class="row">
			<h3>Donate</h3>
				<input type="text" id="donation">
				<button id="donate" class="btn btn-success" type="button" onclick="donate('<?php echo $uname?>', '<?php echo $id?>')">Support the cause!</button> <br>
				<p id="antiocd" style="color:red;font-size:70%"></p>
			</div>
		</div>
	</body>
</html>











