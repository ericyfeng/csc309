<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	date_default_timezone_set('America/Toronto');

	if (!isset($_SESSION["sessid"]) && $_SESSION["sessid"] = "" ) {
		$_SESSION["errmsg"] = "Plase sign in first";
		$_SESSION["login"] = 0;
		header("Location: index.php");
		exit();
	}	

	$sessid = $_SESSION["sessid"];
	$dbconn = pg_connect("dbname=d8dt3b69jeev6n host=ec2-50-19-249-214.compute-1.amazonaws.com port=5432 user=fhntmyljqrdquf password=vgJO4ZQS8Mi7OceXpIzk_dYL0- sslmode=require");
	$verify = "select 1 from session where sessionid=$1";
	pg_prepare($dbconn, "verify", $verify);
	$result = pg_execute($dbconn, "verify", array($sessid));
	$row = pg_fetch_row($result);
	$_SESSION["login"] = 1;	

	if ($row[0] != 1) {
		$_SESSION["errmsg"] = "Please sign in again";
		$_SESSION["login"] = 0;
		header("Location: index.php");
		exit();
	}

	$expiry = "select expiration from session where sessionid=$1";
	pg_prepare($dbconn, "expiry", $expiry);
	$result = pg_execute($dbconn, "expiry", array($sessid));
	$row = pg_fetch_row($result);
	$dbdate = new DateTime($row[0]);
	if($dbdate < new DateTime())
	{
		$_SESSION["login"] = 0;		
		header("Location: index.php");
		exit();
	}
?>