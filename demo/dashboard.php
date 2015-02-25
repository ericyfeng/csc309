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
				<!--It's a quirky method by using a form with all hidden elements but it works-->
				<form action="profile.php" method="POST">
					<input type="hidden" name="user" value="$uname">
					<input type="hidden" name="fname" value="$row[0]">
					<input type="hidden" name="lname" value="$row[1]">
					<input type="submit" value="My Profile">
				</form>


				<h1> <?php echo "$row[0] $row[1]"?>'s projects</h1>
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
				$others = "select projid, description from initiator natural join project where email !=$1";
				pg_prepare($dbconn, "others", $others);
				$result = pg_execute($dbconn, "others", array($uname));

				while($row = pg_fetch_row($result))
				{?>
					<a href="overview.php?p=<?php echo $row[0]?>&user=<?php echo $uname?>"> <?php echo $row[1]?></a>
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
