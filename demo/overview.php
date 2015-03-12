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
		//for ajax updating the current $$ amount after you donate for INSTANT GRATIFICATION
		function donate(sessid, projid)
		{
			var donation = document.getElementById("donation").value;
			if(donation < 1)
			{
				document.getElementById("antiocd").innerHTML="How can you donate a negative amount???"
				return;
			}
			var newAmount = new XMLHttpRequest();
			newAmount.onreadystatechange= function ()
			{
				document.getElementById("current").innerHTML="$"+newAmount.responseText;
			}
			newAmount.open("POST", "addmoney.php", true);
			newAmount.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			newAmount.send("sessid="+sessid+"&projid="+projid+"&amount="+donation);
		}

		//for ajax updating the current rating so you can see your impact right away and warn you of rating more than once
		function rate(sessid, projid)
		{
			var ratingGroup = document.getElementsByName("rating");
			var rating;
			for(var i=0; i < 10; i++)
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
			ajax.send("sessid="+sessid+"&projid="+projid+"&rating="+rating);
		}

		function urate(sessid, email, disp)
		{
			var uratingGroup = document.getElementsByName("urating");
			var urating;
			for(var i=0; i < 10; i++)
			{
				if(uratingGroup[i].checked)
				{
					urating=i+1; //ratings start at 1, button counting starts at 0
					break;
				}
			}
			var ajax = new XMLHttpRequest();
			ajax.onreadystatechange = function ()
			{
				document.getElementById(disp).innerHTML=ajax.responseText;
			}
			ajax.open("POST", "addurating.php", true);
			ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			ajax.send("sessid="+sessid+"&ratee="+email+"&urating="+urating);
		}

		function addcomment(pid, sessid, fname, lname)
		{
			var comment = document.getElementById("newcomment").value;
			var ajax = new XMLHttpRequest();
			var history = document.getElementById("commentHistory");
			//don't really need ajax here but this is a convenient way to send the info
			//	to the db and add the new comment in at the same time
			ajax.open("POST", "addcomment.php", true);
			ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			ajax.send("sessid="+sessid+"&pid="+pid+"&comment="+comment);

			var newc = document.createElement("li");
			newc.innerHTML=fname + " " + lname+"<ul><li>"+comment+"</li></ul>";
			history.appendChild(newc);
		}
		</script>
		<title>Simple Project Overview</title>
	</head>

	<body>
	<?php
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		date_default_timezone_set('America/Toronto');

		$id = $_GET["p"];
		$sessid = $_GET["sessid"];
		$email = $_SESSION["email"];
		$fname = $_SESSION["fname"];
		$lname = $_SESSION["lname"];

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

		//the session id # is real and unexpired... that's good
		//	you don't want people donating under someone else's name.
		$summary = "select * from project where projid=$1";
		pg_prepare($dbconn, "summary", $summary);
		$result = pg_execute($dbconn, "summary", array($id));
		$row = pg_fetch_row($result);
		$rating = $row[8];
		$longdesc = $row[9];
		$exp = $row[4];
		$expDate = new DateTime($exp);
		if($dbdate > $expDate)
		{
			$finished = 1;
		}
		else
		{
			$finished = 0;
		}
	?>

		<!--Black nav bar-->
		<nav class="navbar navbar-inverse">
			<div class="container-fluid">
				<div class="navbar-header">
					<div class="navbar-brand"><?php echo $fname?> <?php echo $lname?>'s Scrap Funder</div>
					</div>

					<div class="navbar-nav navbar-right">

						<!--My profile button-->
						<a href="profile.php?sessid=<?php echo $sessid?>" class="navbar-btn btn btn-primary">
							<span class="glyphicon glyphicon-user"></span> My Profile
						</a>
							
						<!--Real log out button-->
						<a href="logout.php?sessid=<?php echo $sessid?>" class="navbar-btn btn btn-primary"">
							<span class="glyphicon glyphicon-off"></span> Log Out
						</a>
						</div>
					</div>
				</nav>

		<div class="container">

			<div class="row">

			<!--Print project name-->
			<h1> <?php echo "$row[5]"?> </h1>

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

			<!--Project summary information-->
			<table class="table table-bordered table-striped">
				<tr><td><b>Starting Date:</b></td><td><?php echo "$row[3]"?></td></tr>
				<tr><td><b>End Date:</b></td><td><?php echo "$exp"?></td></tr>
				<tr><td><b>Location:</b></td><td><?php echo "$row[6]"?></td></tr>
				<tr><td><b>Current Fudning:</b></td><td><span id="current">$<?php echo "$row[2]"?></span></td></tr>
				<tr><td><b>Target Fuding:</b></td><td>$<?php echo "$row[1]"?></td></tr>
			</table>
			</div>

			<!--The initiator's long description of a project-->
			<div class="row">
				<h4>Detailed Information About the Project:</h4>
				<p><?php echo $longdesc?></p>
			</div>

			<!--Project initiator information-->
			<div class="row">
				<h3>Project Initiators</h3>
				<ul>
					<?php
						$inits = "select fname, lname, reputation, email from initiator natural join users where projid=$1";
						pg_prepare($dbconn, "inits", $inits);
						$result = pg_execute($dbconn, "inits", array($id));
						while ($row = pg_fetch_row($result))
						{
							$initfname = $row[0];
							$initlname = $row[1];
							$initrep = $row[2];
							$initemail = $row[3];
						?>
							<li><?php echo"$initfname $initlname"?></li>
							<ul>
								<li><b>Reputation</b>: <span id="<?php echo 'rep_' . $initemail?>">
															<?php if($initrep > 0)
															{
																echo $initrep;
															}
															else
															{
																echo "Be the first to rate $initfname";
															}?></span></li>
								<li>
									<input type="radio" name="urating" value="1"> 1
									<input type="radio" name="urating" value="2"> 2
									<input type="radio" name="urating" value="3"> 3
									<input type="radio" name="urating" value="4"> 4
									<input type="radio" name="urating" value="5"> 5
									<input type="radio" name="urating" value="6"> 6
									<input type="radio" name="urating" value="7"> 7
									<input type="radio" name="urating" value="8"> 8
									<input type="radio" name="urating" value="9"> 9
									<input type="radio" name="urating" value="10"> 10
									<button type="button" class="btn btn-success" id="rate" onclick="urate('<?php echo $sessid?>', '<?php echo $initemail?>', '<?php echo 'rep_' . $initemail?>')"> Rate <?php echo $initfname?>
								</li>
							</ul>
						<?php
						}
					?>
				</ul>
			</div>

			<!--The rating area-->
			<div class="row">
			<h3>Rate this project</h3>
				<p><b>Current Rating:</b> <span id="liverating"><?php echo $rating?></span></p>
				<input type="radio" name="rating" value="1"> 1
				<input type="radio" name="rating" value="2"> 2
				<input type="radio" name="rating" value="3"> 3
				<input type="radio" name="rating" value="4"> 4
				<input type="radio" name="rating" value="5"> 5
				<input type="radio" name="rating" value="6"> 6
				<input type="radio" name="rating" value="7"> 7
				<input type="radio" name="rating" value="8"> 8
				<input type="radio" name="rating" value="9"> 9
				<input type="radio" name="rating" value="10"> 10
				<button type="button" class="btn btn-success" id="rate" onclick="rate('<?php echo $sessid?>', '<?php echo $id?>')">Rate Project
			</div>

			<!--The donation area: the main attraction-->
			<div class="row">
			<h3>Donate</h3>
				<?php
				if($finished == 0)
				{
				?>
				<input type="text" id="donation">
				<button id="donate" class="btn btn-success" type="button" onclick="donate('<?php echo $sessid?>', '<?php echo $id?>')">Support the cause!</button> <br>
				<!--Warning box about donating twice in a short period of time-->
				<p id="antiocd" style="color:red;font-size:70%"></p>
				<?php 
				} 
				else
				{
					echo"<p>This project has expired</p>";
				}?>
			</div>

			<!--The comments section-->
			<div class="row">
				<label for="newcomment" maxlength="200">Comment on the project:</label>
				<textarea class="form-control" rows="4" id="newcomment"></textarea>
				<button class="btn btn-success" onclick="addcomment('<?php echo $id?>', '<?php echo $sessid?>', '<?php echo $fname?>', '<?php echo $lname?>')">Add Comment</button>
				<p><br><b>What others think:</b></p>
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
			</div>
			<div class="row">
				<br><br><br>
			</div>
		</div>
	</body>
</html>











