<!DOCTYPE html>
<html>
	<head>
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
				echo "Logged in as: $uname";
				$getname = "select fname, lname from users where email=$1";
				pg_prepare($dbconn, "getname", $getname);
				$result = pg_execute($dbconn, "getname", array($uname));
				$row = pg_fetch_row($result);

				echo"
					<form action=\"profile.php\" method=\"POST\">
						<input type=\"hidden\" name=\"user\" value=\"$uname\">
						<input type=\"hidden\" name=\"fname\" value=\"$row[0]\">
						<input type=\"hidden\" name=\"lname\" value=\"$row[1]\">
						<input type=\"submit\" value=\"My Profile\">
					</form>
					";

				echo "<h1>$row[0] $row[1]'s projects</h1>";
				echo "<table>";
				echo "<tr>
						<td>Description</td>
						<td>Current $$</td>
						<td>Target $$</td>
					</tr>";
				$myproj = "select description, curramount, goalamount from initiator natural join project where email=$1";
				pg_prepare($dbconn, "myproj", $myproj);
				$result = pg_execute($dbconn, "myproj", array($uname));
				while($row = pg_fetch_row($result))
				{
					echo "<tr>
							<td>$row[0]</td>
							<td>$row[1]</td>
							<td>$row[2]</td>
						</tr>";
				}
				echo "</table>";
			}
			else
			{
				echo "invalid credentials";
			}
		?>
		<h3> testing 1 2 3 </h3>
	</body>
</html>
