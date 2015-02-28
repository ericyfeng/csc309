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
				$getname = "select fname, lname from users where email=$1";
				pg_prepare($dbconn, "getname", $getname);
				$result = pg_execute($dbconn, "getname", array($uname));
				$row = pg_fetch_row($result);
			?>
				<h1>Welcome <?php echo "$row[0] $row[1]"?> to Crowd Funder</h1>

				<button onclick="location.href='profile.php?fname=<?php echo $row[0]?>&lname=<?php echo $row[1]?>&user=<?php echo $uname?>'">My Profile</button>
				<button onclick="location.href='newproject.php?user=<?php echo $uname?>'">*New Project</button>

				<h3>My Projects</h3>
				<table>
				<tr>
					<td>Description</td>
					<td>Current Funding</td>
					<td>Target Funding</td>
				</tr>

				<?php
				$myproj = "select description, curramount, goalamount, projid from initiator natural join project where email=$1";
				pg_prepare($dbconn, "myproj", $myproj);
				$result = pg_execute($dbconn, "myproj", array($uname));
				while($row = pg_fetch_row($result))
				{
					echo "<tr>
							<td><a href=\"projhistory.php?p=$row[3]\">$row[0]</a></td>
							<td>\$$row[1]</td>
							<td>\$$row[2]</td>
						</tr>";
				}?>
				</table>
			
				<h3>Other Projects</h3>

				<?php
				$others = "select distinct projid, description from initiator natural join project where (projid) not in (select projid from initiator where email=$1)";
				pg_prepare($dbconn, "others", $others);
				$result = pg_execute($dbconn, "others", array($uname));

				while($row = pg_fetch_row($result))
				{?>
					<a href="overview.php?p=<?php echo $row[0]?>&user=<?php echo $uname?>"> <?php echo $row[1]?></a><br>
				<?php
				}
				?>

			<?php
			}
			else
			{
				echo "invalid credentials";
			}
		?>

	</body>
</html>
