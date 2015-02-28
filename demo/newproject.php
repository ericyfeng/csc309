<!DOCTYPE html>
<html>
	<head>
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
		?>

		<h1>Create a New Project</h1>
		<form action="newproject.php" onsubmit="return validate()" method="POST">
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
			<input type="hidden" name="user" value="<?php echo $_GET['user']?>"></input>
		</form>
		<p id="warning" style="color:red;font-size:70%"></p>

		<?php
			$user = $_GET["user"];
			$goalamount = $_POST["goalamount"];
			if($goalamount != null)
			{
				$dbconn = pg_connect("dbname=cs309 user=Daniel");
				$user = $_POST["user"];
				$description = $_POST["description"];
				$location = $_POST["location"];
				$month = $_POST["month"];
				$day = $_POST["day"];
				$year = $_POST["year"];
				$date = $year . "-" . $month . "-" . $day;
				
				$newproj = "insert into project (goalamount, curramount, startdate, enddate, description, location, popularity, rating) values ($1, 0, $2, $3, $4, $5, 100, 100)";
				pg_prepare($dbconn, "newproj", $newproj);
				pg_execute($dbconn, "newproj", array($goalamount, date("Y-m-d"), $date, $description, $location));

				$findid = "select projid from project where description=$1";
				pg_prepare($dbconn, "findid", $findid);
				$result = pg_execute($dbconn, "findid", array($description));
				$row = pg_fetch_row($result);
				$id = $row[0];
				
				$newinit = "insert into initiator values ($1, $2)";
				pg_prepare($dbconn, "newinit", $newinit);
				pg_execute($dbconn, "newinit", array($id, $user));
			}
		?>
	</body>
</html>






