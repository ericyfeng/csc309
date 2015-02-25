<!DOCTYPE html>
<html>
	<head>
		<title>Simple History</title>
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
	?>
		<h3> <?php echo "$row[5]"?> </h3>
		<ul>
			<li><b>Starting Date:</b> <?php echo "$row[3]"?></li>
			<li><b>End Date:</b> <?php echo "$row[4]"?></li>
			<li><b>Location:</b> <?php echo "$row[6]"?></li>
			<li><b>Current Fudning:</b> <?php echo "$row[2]"?></span></li>
			<li><b>Target Fuding:</b> $<?php echo "$row[1]"?></li>
		</ul>		
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











