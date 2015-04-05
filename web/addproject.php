<!DOCTYPE html>
<html>
<head>
	<?php 
		include("template/head.php"); 
		include("backend/checksession.php");
		$fname = $_SESSION["fname"];
		$lname = $_SESSION["lname"];
	?>

	<title>New Project</title>
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

	<div class="container mt">
		<div class="row">	
			<!-- Article main content -->
			<article class="col-xs-12 maincontent">
				
				<div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
					<div class="panel panel-default">
						<div class="panel-body">
							<h3 class="thin text-center">Creating a New Project</h3>
							<hr>	
							<form method="POST" action="backend/addproj.php" onsubmit="return validate()">
								<div class="top-margin">
									<label>Title</label>
									<input type="text" class="form-control" name="description" required>
								</div>
								<div class="top-margin">
									<label>Required Funding</label>
									<input type="text" class="form-control" name="goalamount" required>
								</div>								
								<div class="top-margin">
									<label>End Date: </label>
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
								</div>
								<div class="top-margin">
									<label>Location</label>
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
								</div>
								<div class="top-margin">
									<label>Video Description</label>
									<input type="text" class="form-control" name="video"></input>
								</div>	

								<div class="top-margin">
									<label>Image URL</label>
									<input type="text" class="form-control" name="picture"></input>
								</div>									
								<div class="top-margin">
									<label>Description</label>
									<textarea type="text" class="form-control" name="longdesc" rows="6"></textarea>
									<!--<textarea rows='5' class="form-control" name="description" id="description"></textarea>-->
								</div>																		
								<div id="errmsg"></div>
								<br>



								<div class="row">
									<div class="col-lg-offset-8 col-lg-4 text-right">
										<!--<button id="login" class="btn btn-action" type="submit" onclick="rm()">Sign in</button>-->
										<input type="submit" class="btn-theme" value="Submit">
									</div>
								</div>
							</form>

						</div>
					</div>
				</div>
				
			</article>  <!-- /Article -->
		</div>  <!--/row -->
	</div> <!-- container -->

		<! ========== FOOTER ======================================================================================================== 
	=============================================================================================================================>    
	
	<?php include("template/footer.php"); ?>

	<script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/retina.js"></script>
</body>
</html>
