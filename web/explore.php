<!DOCTYPE html>
<html lang="en">
  <head>

  	<?php include("template/head.php"); ?>
    <title>Explore</title>

    <link href="assets/css/explore.css" rel="stylesheet">       

  </head>

  <body>
  	<?php
		//enable php debugging
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		date_default_timezone_set('America/Toronto');
		$dbconn = pg_connect("dbname=d8dt3b69jeev6n host=ec2-50-19-249-214.compute-1.amazonaws.com port=5432 user=fhntmyljqrdquf password=vgJO4ZQS8Mi7OceXpIzk_dYL0- sslmode=require");

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

]	
	<!--Surround the body in its own container-->
	<div class="container-fliud">
		<!--Everything in the body will be in a single row-->
		<div class="row">
			<!--The left column is just has the mort text-->
			<div class="col-sm-2">
				<div id="menu">
					<div class="container-fliud pull-right">
						<ul class="nav">
							<li>Categories</li>
								<li><a onclick="filtertags('commgeneric')">ALL</a></li>
								<?php
									//for now just pull any project in the backend to display
									$categories = "select description from community;";
									pg_prepare($dbconn, "cat", $categories);
									$result = pg_execute($dbconn, "cat", array());
									while ($row = pg_fetch_row($result)) {
								?>						
								<li><a href="#"><?= $row[0] ?></a></li>
								<?php
									}
								?>								
						</ul>
					</div>
				</div>
			</div>


		<! ========== HEADERWRAP ==================================================================================================== 
		=============================================================================================================================>
			<!--The original body can go in the 2nd column-->
			<div class="col-md-8">
			    <div id="headerwrap">
			    	<div class="container">
						<div class="row centered mt">
							<div class="col-lg-8 col-lg-offset-2 mt">
								<h1 class="titletext">Explore All Live Project! (Page under development)</h1>
				                <div id="searchbar">
				                	<div class="center-block">
				                    	<div class="input-group">
				                      		<input type="text" class="form-control" placeholder="Start Exploring!">
				                      		<span class="input-group-btn">
				                        		<button class="btn btn-default" type="button">search</button>
				                      		</span>
				                    	</div><!-- /input-group -->
				                  	</div><!-- /.col-lg-6 -->
				                </div><!-- /.row -->					
							</div>
							
						</div><!-- /row -->
			    	</div><!-- /container -->
			    </div> <!-- /headerwrap -->

		<! ========== PROJECTS ==================================================================================================== 
		=============================================================================================================================>    
				<div class="container">	

					<div class="row mt centered ">
						<div class="col-lg-4 col-lg-offset-4">
							<h3>Featured Projects</h3>
							<hr>
						</div>
					</div><!-- /row -->

					<div class="row mt">
					<?php
						//for now just pull any project in the backend to display
						$featured = "select projid, goalamount, curramount, startdate, enddate, t1.description, locname, popularity, rating, longdesc, community.description from 
							(select * from project natural join communityendorsement natural join location order by rating desc limit 6) t1, community where community.commid=t1.commid;";
						pg_prepare($dbconn, "featured", $featured);
						$result = pg_execute($dbconn, "featured", array());
						while ($row = pg_fetch_row($result)) {
					?>
						<?php 
							$progress = round(($row[2] / $row[1]), 2) * 100;
							$enddate = new DateTime($row[4]);
							$today = new DateTime(date("Y-m-d"));
							$remaining = date_diff($today, $enddate) ;
						?>
						<div class="col-lg-4 col-md-4 col-xs-12 desc">
							<a class="b-link-fade b-animate-go" href="#"><img width="350" src="assets/img/portfolio/port04.jpg" alt="" />
								<div class="b-wrapper">
								  	<h4 class="b-from-left b-animate b-delay03"> <?= $row[5] ?></h4>
								  	<p class="b-from-right b-animate b-delay03">Read More. (please log in first WILL BE CHANGED)</p>
								</div>
							</a>
							<p><?= $row[5] ?></p>
							<p class="lead"><?= $row[9] ?></p>
							<div class="progress">
			  					<div class="progress-bar progress-bar-theme" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?= $progress ?>%;">
			    				<?= $progress ?>%
			 					</div>
							</div>			
							<hr-d>
							<p class="time"><i class="fa fa-tag"></i> <?= $row[10] ?> | <i class="fa fa-calendar"></i> <?= $remaining->days ?> days left | <i class="fa fa-map-marker"></i> <?= $row[6] ?></p>

						</div><!-- col-lg-4 -->
						<?php
							}
						?>	
					</div><!-- /row -->
				</div><!-- /container -->
			</div> <!-- col-md-8 -->
		</div><!-- body row -->
	</div> <!-- container-fliud -->


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

	<! ========== MODAL ======================================================================================================== 
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
