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
		//for ajax feedback on whether your attempt to add a person as an initiator worked
		function addinit(sessid, pid)
		{
			var email = document.getElementById("newinit").value;
			var ajax = new XMLHttpRequest();
			ajax.onreadystatechange = function ()
			{
				document.getElementById("status").innerHTML=ajax.responseText;
			}
			ajax.open("POST", "addinit.php", true);
			ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			ajax.send("sessid="+sessid+"&email="+email+"&pid="+pid);
		}

		//for ajax updating of your project's tag list
		function addtag(sessid, pid)
		{
			var commsel = document.getElementById("newtags");
			var commid = commsel.value;
			var ajax = new XMLHttpRequest();
			ajax.onreadystatechange = function ()
			{
				document.getElementById("tags").innerHTML=ajax.responseText;
			}
			ajax.open("POST", "addtag.php", true);
			ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			ajax.send("sessid="+sessid+"&commid="+commid+"&pid="+pid);
		}
		</script>

		<title>Your Project History</title>
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
		$summary = "select * from project where projid=$1";
		pg_prepare($dbconn, "summary", $summary);
		$result = pg_execute($dbconn, "summary", array($id));
		$row = pg_fetch_row($result);

		//for an esthetic touch, don't display a rating of 0 if nobody has voted yet
		if($row[8] == 0) 
		{
			$rating="No ratings yet";
		}
		else
		{
			$rating=$row[8];
		}
		$longdescription = $row[9];
	?>

		<!--Black nav bar-->
		<nav class="navbar navbar-inverse">
			<div class="container-fluid">
				<div class="navbar-header">
					<div class="navbar-brand"><?php echo $fname?> <?php echo $lname?>'s Scrap Funder</div>
					</div>

					<div class="navbar-nav navbar-right">

						<!--Add Another Tag button-->
						<a href="#" class="navbar-btn btn btn-danger" data-toggle="modal" data-target="#addtag">
							<span class="glyphicon glyphicon-tag"></span> +
						</a>
					
						<!--Add Another Owner button-->
						<a href="#" class="navbar-btn btn btn-success" data-toggle="modal" data-target="#addowner">
							<span class="glyphicon glyphicon-user"></span> +
						</a>

						<!--My profile button-->
						<a href="profile.php?sessid=<?php echo $sessid?>" class="navbar-btn btn btn-primary">
							<span class="glyphicon glyphicon-user"></span> My Profile
						</a>
							
						<!--Real functioning log out button-->
						<a href="logout.php?sessid=<?php echo $sessid?>" class="navbar-btn btn btn-primary">
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
					<input type="button" class="btn btn-warning" onclick="addinit('<?php echo $sessid?>', '<?php echo $id?>')" value="+">
				</div>
				</div>
			</div>
		</div>

		<!--Add tag popup-->
		<div class="modal fade" id="addtag" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
			<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4>Add A Tag</h4>
				</div>
				<div class="modal-body">
					<select id="newtags" name="newtags">
						<?php
							$unused = "select * from community where (commid) not in (select commid from communityendorsement where projid=$1)";
							pg_prepare($dbconn, "unused", $unused);
							$result2 = pg_execute($dbconn, "unused", array($id));
							while($row2 = pg_fetch_row($result2))
							{
								echo "<option value=\"$row2[0]\">$row2[1]</option>";
							}
						?>
					</select>
					<input type="button" class="btn btn-warning" onclick="addtag('<?php echo $sessid?>', '<?php echo $id?>')" value="+">
				</div>
				</div>
			</div>
		</div>

		<div class="container">
			<h3><b><?php echo "$row[5]"?></b></h3>

			<!--Project tags-->
			<ul class="list-inline" id="tags">
				<?php
					$currentTags = "select description from communityendorsement natural join community where projid=$1";
					pg_prepare($dbconn, "currentTags", $currentTags);
					$result3 = pg_execute($dbconn, "currentTags", array($id));
					while($row3 = pg_fetch_row($result3))
					{
						echo "<li class=\"glyphicon glyphicon-tag btn btn-danger\"> $row3[0]</li> ";
					}
				?>
			</ul>

			<!--Project stats-->
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

			<!--Used to display the ajax result of whether adding another person as an initiator worked or not-->
			<p id="status" style="color:blue;font-size:70%"></p>

			<!--Show the user what he typed in as the descriptive paragraph to the public-->
			<h4>Description to the public:</h4>
			<p><?php echo $longdescription?></p>

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
			
			<!--Copy and pasted comments area-->
			<h3><b>What others think:</b></h3>
			<ul id="commentHistory">
				<?php
					$projcomments = "select fname, lname, comment from comment natural join users where projid=$1 order by cid asc";
					pg_prepare($dbconn, "projcomments", $projcomments);
					$result = pg_execute($dbconn, "projcomments", array($id));
					while($row = pg_fetch_row($result))
					{
						echo "<li>$row[0] $row[1]
								<ul><li><p>$row[2]</p></li></ul>
							</li>";
					}
				?>
			</ul>
			<br><br><br>
		</div>
	</body>
</html>











