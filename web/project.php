<!DOCTYPE html>
<html lang="en">
  <head>
  	<?php
  		include("template/head.php"); 
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		date_default_timezone_set('America/Toronto');  
  		$dbconn = pg_connect("dbname=d8dt3b69jeev6n host=ec2-50-19-249-214.compute-1.amazonaws.com port=5432 user=fhntmyljqrdquf password=vgJO4ZQS8Mi7OceXpIzk_dYL0- sslmode=require");
  		$projid = $_GET["projid"];

  		$project = "select * from project natural join location";
  		pg_prepare($dbconn, "project", $project);
  		$result = pg_execute($dbconn, "project", array($projid));
  		$row = pg_fetch_array($result);

  	?>
    <title><?php echo $row["title"] ?></title>

    <!-- Custom styles for this template -->
    <link href="assets/css/project.css" rel="stylesheet"> 

   
  </head>

  <body>
	<! ========== NAV BAR ==================================================================================================== 
	=============================================================================================================================>

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

	<! ========== MAIN ======================================================================================================== 
	=============================================================================================================================> 

	<div class="container">
		<div class="row centered top-margin">
			<div class="col-sm-8 col-sm-offset-2">
				<h1><?php echo $row["title"] ?></h1>
				<hr>
			</div>
		</div><!-- /row -->

		<div class="row">
			<div class="col-lg-8 col-md-8 col-xs-12">
				<iframe width="720" height="483" src="<?= $row['video']?>" frameborder="0" allowfullscreen></iframe>
			</div>
			<div class="col-lg-4 col-md-4 col-xs-12">
				<div class="container-fliud project-info">
					<div class="current-balance">
						<h3>$<?php echo $row["curramount"] ?></h3>
						<p class="lead">Raised of <?php echo $row["goalamount"] ?> Goal</p>			
					</div>						
					<div class="progress">
						<?php 
							$progress = round(($row["curramount"] / $row["goalamount"]), 2) * 100;
							$enddate = new DateTime($row["enddate"]);
							$today = new DateTime(date("Y-m-d"));
							$remaining = date_diff($today, $enddate) ;
						?>
						<div class="progress-bar progress-bar-theme" role="progressbar" aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $progress ?>%;">
			    				<?= $progress ?>%
			 			</div>
					</div>									
					<div class="remain-time">
						<p class="lead"><i class="fa fa-clock-o"></i> <?php echo $remaining->days ?>  Days Left</p>
					</div>
					<?php
					$funders = "select distinct count(email) from funder where projid=$1";
					pg_prepare($dbconn, "funders", $funders);
					$result2 = pg_execute($dbconn, "funders", array($projid));
					$row2 = pg_fetch_row($result2);
					?>
					<p class="lead"><i class="fa fa-user"></i> <?= $row2[0]?> Funders</p>
					<div class="tags">		
						<p class="lead"><i class="fa fa-tag"></i> <?php echo $row["description"] ?></p>
						<p class="lead"><i class="fa fa-map-marker"></i> <?php echo $row["locname"] ?></p>
					</div>
					<div class="form-group">
						<input type="text" class="form-control">
						<button type="sub" class="btn btn-danger btn-lg btn-block">Fund the project!</button>
						</div>	    	

					<div class="p-initiator">
						<h4>Initiated by: </h4>
						<a href="#">
							<p class="lead"><i class="fa fa-rocket"></i> <?php echo $row["fname"] . " " . $row["lname"] ." (" . $row["reputation"] . ")" ;?></p>
						</a>
					</div>

					<div class="form-group">
						<h4>Rate initiator:</h4>
						<select class="form-control" id="reputation" name="reputation">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
							<option value="6">6</option>
							<option value="7">7</option>
							<option value="8">8</option>
							<option value="9">9</option>
							<option value="10">10</option>
						</select>
						<button class="btn-theme">submit</button>
					</div>

					<div class="form-group">
						<h4 class="rate">Rate Project:</h4>
						<select class="form-control" id="projrate" name="projrate">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
							<option value="6">6</option>
							<option value="7">7</option>
							<option value="8">8</option>
							<option value="9">9</option>
							<option value="10">10</option>
						</select>
						<button class="btn-theme">submit</button>
					</div>

				</div>
			</div>
		</div>
		<hr>

		<div class="row">
			<h2>MORE ABOUT PROJECT</h2>
			<p><?php echo $row["longdesc"] ?></p>
		</div>

		<hr>
		<div class="row">
			<h2>Comments</h2>
				<label for="newcomment" maxlength="200">Comment on the project:</label>
				<textarea class="form-control" rows="4" id="newcomment"></textarea>
				<button class="btn-theme" onclick="addcomment('<?php echo $id?>', '<?php echo $sessid?>', '<?php echo $fname?>', '<?php echo $lname?>')">Add Comment</button>
				<ul id="commentHistory">
					<?php
						$projcomments = "select fname, lname, comment from comment natural join users where projid=$1 order by cid asc";
						pg_prepare($dbconn, "projcomments", $projcomments);
						$result = pg_execute($dbconn, "projcomments", array($projid));

						while($row = pg_fetch_row($result))
						{
							echo "<h4>$row[0] $row[1]:</h4>
								<li><p>$row[2]</p></li>";
								
						}
					?>

				</ul>
		</div>

	</div>

	<! ========== CALL TO ACTION BAR =============================================================================================== 
	=============================================================================================================================>    
	<div id="cta-bar">
		<div class="container">
			<div class="row centered">
				<a href="#signup" data-toggle="modal"><h4>Ready For The Next Step? Sign Up NOW!</h4></a>
			</div>
		</div><!-- /container -->
	</div><!-- /cta-bar -->
	
	<! ========== FOOTER ======================================================================================================== 
	=============================================================================================================================>    
	
	<?php include("template/footer.php"); ?>	

	<! ========== MODALS ======================================================================================================== 
	=============================================================================================================================>    

	<?php include("template/usermodals.php"); ?>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/retina.js"></script>


  	<script>
		$(window).scroll(function() {
			$('.si').each(function(){
			var imagePos = $(this).offset().top;
	
			var topOfWindow = $(window).scrollTop();
				if (imagePos < topOfWindow+400) {
					$(this).addClass("slideUp");
				}
			});
		});
	</script>    



  
  </body>
</html>
