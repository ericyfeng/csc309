<!DOCTYPE html>
<html>
	<head>
		<title>Simple Project Overview</title>
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
	</head>

	<body>
	<?php
		error_reporting(E_ALL);
		ini_set('display_errors', 1);

		$id = $_GET["p"];
		$uname = $_GET["user"];
		$dbconn = pg_connect("dbname=cs309 user=Daniel");
		$summary = "select * from project where projid=$1";
		pg_prepare($dbconn, "summary", $summary);
		$result = pg_execute($dbconn, "summary", array($id));
		$row = pg_fetch_row($result);
		$rating = $row[8];
	?>
		<h1>Project Summary</h1>
		<h3> <?php echo "$row[5]"?> </h3>
		<ul>
			<li><b>Starting Date:</b> <?php echo "$row[3]"?></li>
			<li><b>End Date:</b> <?php echo "$row[4]"?></li>
			<li><b>Location:</b> <?php echo "$row[6]"?></li>
			<li><b>Current Fudning:</b> <span id="current">$<?php echo "$row[2]"?></span></li>
			<li><b>Target Fuding:</b> $<?php echo "$row[1]"?></li>
		</ul>
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
		<h3>Rate this project</h3>
			<p><b>Current Rating:</b> <span id="liverating"><?php echo $rating?></span></p>
			<input type="radio" name="rating" value="1"> 1
			<input type="radio" name="rating" value="2"> 2
			<input type="radio" name="rating" value="3"> 3
			<input type="radio" name="rating" value="4"> 4
			<input type="radio" name="rating" value="5"> 5
			<input type="button" id="rate" value="Rate!" onclick="rate('<?php echo $id?>', '<?php echo $uname?>')">

		<h3>Donate</h3>
			<input type="text" id="donation">
			<button id="donate" type="button" onclick="donate('<?php echo $uname?>', '<?php echo $id?>')">Support the cause!</button> <br>
			<p id="antiocd" style="color:red;font-size:70%"></p>
	</body>
</html>











