<!DOCTYPE html>
<?php
	session_start();
?>

<html>
	<head>
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

		<!-- jQuery library -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

		<!-- Latest compiled JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

		<title>Simple Project Creation</title>
		<script>
			function validate() //get it: valid date
			{
				var month = document.getElementById("month").value;
				var day = document.getElementById("day").value;
				var year = document.getElementById("year").value;
				var todayyear = new Date().getFullYear();
				var todaymonth = new Date().getMonth()+1;
				var todayday = new Date().getDate();
				
				if(year == todayyear)
				{
					if(month < todaymonth)
					{
						document.getElementById("warning").innerHTML="Project can't end before it starts";
						return false;
					}
					if(month == todaymonth)
					{
						if(day < todayday)
						{
							document.getElementById("warning").innerHTML="Project can't end before it starts";
							return false;
						}
						else if (day == todayday)
						{
							document.getElementById("warning").innerHTML="Project should last at least a day";
							return false;
						}
					}
				}
				
				if(month==4 || month==6 || month==9 || month==11)
				{
					if(day == 31) 
					{	
						document.getElementById("warning").innerHTML="Date does not exist";
						return false;
					}
				}
				if(month==2)
				{
					if((year%4 == 0) && (day > 29))
					{
						document.getElementById("warning").innerHTML="You only get up to 29 in "+year;
						return false;
					}
					else if(day>28) 
					{
						document.getElementById("warning").innerHTML="No Feb 29 in "+year;
						return false;
					}
				}
				document.getElementById("warning").innerHTML="it's ok";
				return true;
			}
		</script>
	</head>

	<body>
		<?php
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
			date_default_timezone_set('America/Toronto');

			$dbconn = pg_connect("dbname=cs309 user=Daniel");

			//check if session id is real or faked
			$sessid = $_GET["sessid"];
			$validnum = "select count(*) from session where sessionid=$1";
			pg_prepare($dbconn, "validnum", $validnum);
			$result = pg_execute($dbconn, "validnum", array($sessid));
			$row = pg_fetch_row($result);
			if($row[0] == 0)
			{
				echo "Please login again"; //do not give hints about the failed login
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
				echo "Please login again";
				exit();
			}

			$fname = $_SESSION["fname"];
			$lname = $_SESSION["lname"];
			$email = $_SESSION["email"];
		?>

		<!--Black nav bar-->
		<nav class="navbar navbar-inverse">
			<div class="container-fluid">
				<div class="navbar-header">
					<div class="navbar-brand"><?php echo $fname?> <?php echo $lname?>'s Scrap Funder</div>
					</div>

					<div class="navbar-nav navbar-right">

						<!--My profile button-->
						<a href="profile.php?fname=<?php echo $fname?>&lname=<?php echo $lname?>&user=<?php echo $uname?>" class="navbar-btn btn btn-primary"">
							<span class="glyphicon glyphicon-user"></span> My Profile
						</a>
							
						<!--Fakeish log out button-->
						<a href="logout.php?sessid=<?php echo $sessid?>" class="navbar-btn btn btn-primary"">
							<span class="glyphicon glyphicon-off"></span> Log Out
						</a>
						</div>
					</div>
				</nav>

		<h1>Create a New Project</h1>
		<form action="addproj.php?sessid=<?php echo $sessid?>" onsubmit="return validate()" method="POST">
			<table>
				<tr>
					<td>Description:</td>
					<td><input type="text" name="description" required></td>
				</tr>
				<tr>
					<td>Required Funding:</td>
					<td><input type="text" name="goalamount" required></td>
				</tr>
				<tr>
					<td>End Date:</td> 
					<td>
						<select id="month" name="month" required>
							<option value="1">January</option>
							<option value="2">February</option>
							<option value="3">March</option>
							<option value="4">April</option>
							<option value="5">May</option>
							<option value="6">June</option>
							<option value="7">July</option>
							<option value="8">August</option>
							<option value="9">September</option>
							<option value="10">October</option>
							<option value="11">November</option>
							<option value="12">December</option>
						</select>
						<select id="day" name="day" required>
							<?php 
								for($i=1; $i<=31; $i++)
								{?>
									<option value="<?php echo $i?>"><?php echo $i?></option>
								<?php
								}
							?>
						</select>
						<select id="year" name="year" required>
							<?php
								date_default_timezone_set("EST");
								$year = date("Y");
								for($i=$year; $i<=$year+5; $i++)
								{?>
									<option value="<?php echo $i?>"><?php echo $i?></option>
								<?php
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td>Location:</td>
					<td><input type="text" name="location" required></input></td>
				</tr>
				<tr><td><input type="submit" value="Let's do it!"></input></td></tr>
			</table>
		</form>
		<p id="warning" style="color:red;font-size:70%"></p>
	</body>
</html>






