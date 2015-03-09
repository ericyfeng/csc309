<?php
	session_start();
?>

<html>
	<head>
		<title>Sign up processing</title>
	</head>
	
	<body>
	<?php
		//enable php debugging
		error_reporting(E_ALL);
		ini_set('display_errors', 1);

		//database connection variable
		$dbconn = pg_connect("dbname=cs309 user=ericfeng");

		//check if email and password match
		$fname = $_POST["fname"];
		$lname = $_POST["lname"];
		$email = $_POST["email"];
		$passwd = $_POST["passwd"];
		$confirm = $_POST["confirm"];
		$verify = "select count(*) from users where email=$1;"
		pg_prepare($dbconn, "verify", $verify);
		$result = pg_execute($dbconn, "verify", array($email));
		$row = pg_fetch_row($result);
		if ($row[0]) {
			$register = "insert into users values ($1, $2, $3, $4, 0);";
			pg_prepare($dbconn, "signup", $register);
			pg_execute($dbconn, "signup", array($email, $fname, $lname, $passwd));
			header("Location: index.php");
			exit();
		} 
		else {
			echo "Please try signing up again";
		}
	?>
	</body>
</html>





