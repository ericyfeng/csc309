<!DOCTYPE html>
<html>
	<head>
		<title>Simple History</title>
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
	</head>
	
	<body>
	<?php
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		
		$id = $_GET["p"];
		$dbconn = pg_connect("dbname=cs309 user=Daniel");
		$summary = "select * from project where projid=$1";
		pg_prepare($dbconn, "summary", $summary);
		$result = pg_execute($dbconn, "summary", array($id));
		$row = pg_fetch_row($result);
		if($row[8]==0) $rating="No ratings yet";
		else $rating=$row[8];
	?>
		<h3> <?php echo "$row[5]"?> </h3>
		<ul>
			<li><b>Starting Date:</b> <?php echo "$row[3]"?></li>
			<li><b>End Date:</b> <?php echo "$row[4]"?></li>
			<li><b>Location:</b> <?php echo "$row[6]"?></li>
			<li><b>Current Fudning:</b> <?php echo "$row[2]"?></span></li>
			<li><b>Target Fuding:</b> $<?php echo "$row[1]"?></li>
			<li><b>Community Rating:</b> <?php echo "$rating"?></li>
		</ul>

		<h3>Add Project Owner</h3>
			<input type="text" id="newinit"></input>
			<input type="button" onclick="addinit('<?php echo $id?>')" value="+">
			<p id="status" style="color:blue;font-size:70%"></p>
		<h3>Donation History</h3>
		<table>
			<tr>
				<td>First Name</td>
				<td>Last Name</td>
				<td>Amount</td>
				<td>Date</td>
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
	</body>
</html>











