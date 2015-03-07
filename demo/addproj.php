<!DOCTYPE html>
<?php
	session_start();
?>

<html>
	<body>
		<?php
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
			date_default_timezone_set('America/Toronto');

			$sessid = $_GET["sessid"];
			$dbconn = pg_connect("dbname=cs309 user=Daniel");

			//check if session id is real or faked
			$validnum = "select count(*) from session where sessionid=$1";
			pg_prepare($dbconn, "validnum", $validnum);
			$result = pg_execute($dbconn, "validnum", array($sessid));
			$row = pg_fetch_row($result);
			if($row[0] == 0)
			{
				echo "Please login again DNE"; //do not give hints about the failed login
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
				echo "Please login again EXP";
				exit();
			}


			//retrieve the information from the form entry fields
			$goalamount = $_POST["goalamount"];
			$email = $_SESSION["email"];
			$description = $_POST["description"];
			$location = $_POST["location"];
			$month = $_POST["month"];
			$day = $_POST["day"];
			$year = $_POST["year"];
			$longdesc = $_POST["longdesc"];
			//merge all the date information into 1 string
			$date = $year . "-" . $month . "-" . $day;
				
			//send project infromation into project table
			$newproj = "insert into project (goalamount, curramount, startdate, enddate, description, location, popularity, rating, longdesc) values ($1, 0, $2, $3, $4, $5, 0, 0, $6)";
			pg_prepare($dbconn, "newproj", $newproj);
			pg_execute($dbconn, "newproj", array($goalamount, date("Y-m-d"), $date, $description, $location, $longdesc));

			//get new project id for setting up initiator
			$findid = "select projid from project where description=$1";
			pg_prepare($dbconn, "findid", $findid);
			$result = pg_execute($dbconn, "findid", array($description));
			$row = pg_fetch_row($result);
			$id = $row[0];
				
			//setup the current person as the initator
			$newinit = "insert into initiator values ($1, $2)";
			pg_prepare($dbconn, "newinit", $newinit);
			pg_execute($dbconn, "newinit", array($id, $email));

			header("Location: dashboard.php?sessid=$sessid");
		?>
	</body>
</html>
