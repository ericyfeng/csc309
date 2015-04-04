<!DOCTYPE html>
<?php
	include("backend/checksession.php");
	$fname = $_SESSION["fname"];
	$lname = $_SESSION["lname"];
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
		function validate()
		{//check to make sure the end date makes sense which means...
		 //	the end date MUST: come after the current date
		 //					   be on a real date (no april 31 or feb 29 in non leap years etc)
			var month = document.getElementById("month").value;
			var day = document.getElementById("day").value;
			var year = document.getElementById("year").value;
			var todayyear = new Date().getFullYear();
			var todaymonth = new Date().getMonth()+1;
			var todayday = new Date().getDate();
				
			//check to make sure the end date is after the current date
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
				
			//april, june, september, november only have 30 days so 31st in these months
			if(month==4 || month==6 || month==9 || month==11)
			{
				if(day == 31) 
				{	
					document.getElementById("warning").innerHTML="Date does not exist";
					return false;
				}
			}

			//february leap year checking
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

			//if all the tests pass, the end date is ok
			return true;
		}
		</script>
	</head>

	<body>

		<!--Black nav bar-->
		<nav class="navbar navbar-inverse">
			<div class="container-fluid">
				<div class="navbar-header">
					<div class="navbar-brand"><?php echo $fname?> <?php echo $lname?>'s Scrap Funder</div>
					</div>

					<div class="navbar-nav navbar-right">

						<!--My profile button-->
						<a href="profile.php?sessid=<?php echo $sessid?>" class="navbar-btn btn btn-primary">
							<span class="glyphicon glyphicon-user"></span> My Profile
						</a>
							
						<!--Fakeish log out button-->
						<a href="logout.php?sessid=<?php echo $sessid?>" class="navbar-btn btn btn-primary">
							<span class="glyphicon glyphicon-off"></span> Log Out
						</a>
						</div>
					</div>
				</nav>

		<!--The main form for new project information-->
		<div class="container">
			<h1>Create a New Project</h1>
			<form action="addproj.php" onsubmit="return validate()" method="POST">
				<table class="table table-bordered table-striped">
					<tr>
						<td>Title:</td>
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
							<?php //use php for setting up the days 1-31 to keep this file readable
								for($i=1; $i<=31; $i++)
								{?>
									<option value="<?php echo $i?>"><?php echo $i?></option>
								<?php
								}
							?>
							</select>
							<select id="year" name="year" required>
							<?php //project must end within the next 5 years. any longer and people will forget
								date_default_timezone_set("America/Toronto");
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
						<td>
							<select id="locid" name="locid" required>
							<?php
								$locs = "select * from location";
								pg_prepare($dbconn, "locs", $locs);
								$result = pg_execute($dbconn, "locs", array());
								while($row = pg_fetch_row($result))
								{?>
									<option value="<?php echo $row[0]?>"><?php echo $row[1]?></option>
								<?php
								}
							?>
							</select>
						</td>
					</tr>

					<!--The long description is a text box input area to make it convenient for the user-->
					<tr>
						<td>Description:</td>
						<td><textarea name="longdesc" required></textarea></td>
					</tr>					
				</table>
				<input type="submit" class="btn btn-success"value="Let's do it!"></input>
			</form>

			<!--Warning area used for feedback on why the end date is invalid if it is-->
			<p id="warning" style="color:red;font-size:70%"></p>
		</div>
	</body>
</html>






