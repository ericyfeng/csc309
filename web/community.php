<!DOCTYPE html>
<html lang="en">
	<head>
		<?php 
			include("template/head.php");
			include("backend/checksession.php");
		?>
		<title>community</title>

		<script>
		function addcomment(commid, fname, lname)
		{
			var comment = document.getElementById("newcomment").value;
			var ajax = new XMLHttpRequest();
			var history = document.getElementById("commentHistory");
			//don't really need ajax here but this is a convenient way to send the info
			//	to the db and add the new comment in at the same time
			ajax.open("POST", "backend/addcc.php", true);
			ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			ajax.send("commid="+commid+"&comment="+comment);

			var newc = document.createElement("li");
			newc.innerHTML="<h4>"+fname + " " + lname+"</h4><li><p>"+comment+"</p></li>";
			history.appendChild(newc);
		}
		</script>
	</head>
	<body>

	<?php 
		include("template/loginnav.php"); 
		$commid = $_GET["commid"];
		$comm_name = "select description from community where commid=$1";	
		pg_prepare($dbconn, "comm_name", $comm_name);
		$result = pg_execute($dbconn, "comm_name", array($commid));
		$row = pg_fetch_row($result);
		$name = $row[0];
	?>

	<div class="container">	
		<h3>The <?= $name?> Community</h3>
		<hr>

		<label for="newcomment" maxlength="200">Discuss <?= $name?></label>
		<textarea class="form-control" rows="4" id="newcomment"></textarea>
		<button class="btn-theme" onclick="addcomment('<?php echo $commid?>', '<?php echo $_SESSION['fname']?>', '<?php echo $_SESSION['lname']?>')">Record</button>
		<hr>

		<ul id="commentHistory">
		<?php
			$projcomments = "select fname, lname, comment from commcomment natural join users where commid=$1 order by ccid asc";
			pg_prepare($dbconn, "projcomments", $projcomments);
			$result = pg_execute($dbconn, "projcomments", array($commid));

			while($row = pg_fetch_row($result))
			{
				echo "<h4>$row[0] $row[1]:</h4>
					<li><p>$row[2]</p></li>";
								
			}
		?>
		</ul>
	</div>	

		<?php include("template/footer.php"); ?>

	</body>
</html>
