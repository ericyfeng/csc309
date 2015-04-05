<!DOCTYPE html>
<html>
	<head>
		<?php 
			include("template/head.php"); 
			include("backend/checksession.php");
			$fname = $_SESSION["fname"];
			$lname = $_SESSION["lname"];
		?>

		<title>Simple Project Creation</title>
		<script src="assets/js/validate.js"></script>
	</head>

	<body>

	<?php
		//enable php debugging
		if($_SESSION["loggedin"] == 1)
		{
			include("template/loginnav.php");
			//include("template/navbar.php");
		}
		else
		{
			include("template/navbar.php");
		}
	?>
		<!--The main form for new project information-->
		<div class="container">
			<h1>Create a New Project</h1>
			<form action="backend/addproj.php" onsubmit="return validate()" method="POST">
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

					<tr>
						<td>Video Description</td>
						<td><input type="text" class="form-control" name="video"></input></td>
					</tr>	
					<tr>
						<td>Image URL</td>
						<td><input type="text" class="form-control" name="picture"></input></td>
					</tr>					
				</table>
				<input type="submit" class="btn-theme"value="Let's do it!"></input>
			</form>

			<!--Warning area used for feedback on why the end date is invalid if it is-->
			<p id="warning" style="color:red;font-size:70%"></p>
		</div>
	</body>
</html>






