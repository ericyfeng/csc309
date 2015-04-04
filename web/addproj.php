
<?php
	session_start();
	$dbconn = pg_connect("dbname=d8dt3b69jeev6n host=ec2-50-19-249-214.compute-1.amazonaws.com port=5432 user=fhntmyljqrdquf password=vgJO4ZQS8Mi7OceXpIzk_dYL0- sslmode=require");
	//retrieve the information from the form entry fields about the new project
	$goalamount = $_POST["goalamount"];
	$email = $_SESSION["email"];
	$description = $_POST["description"];
	$locid = $_POST["locid"];
	$month = $_POST["month"];
	$day = $_POST["day"];
	$year = $_POST["year"];
	$longdesc = $_POST["longdesc"];
	//merge all the date information into 1 string
	$date = $year . "-" . $month . "-" . $day;
		
	//send project infromation into project table
	$newproj = "insert into project (goalamount, curramount, startdate, enddate, description, locid, popularity, rating, longdesc) values ($1, 0, $2, $3, $4, $5, 0, 0, $6)";
	pg_prepare($dbconn, "newproj", $newproj);
	pg_execute($dbconn, "newproj", array($goalamount, date("Y-m-d"), $date, $description, $locid, $longdesc));

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

	//send the person back to the dashboard screen after project creation
	header("Location: dashboard.php");
?>
