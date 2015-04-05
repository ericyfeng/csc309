<!DOCTYPE html>
<html lang="en">
	<head>
		<?php 
			include("template/head.php");
		?>
		<title>HOME</title>
	</head>

	<body>

	<?php
		//enable php debugging
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		date_default_timezone_set('America/Toronto');
		$dbconn = pg_connect("dbname=d8dt3b69jeev6n host=ec2-50-19-249-214.compute-1.amazonaws.com port=5432 user=fhntmyljqrdquf password=vgJO4ZQS8Mi7OceXpIzk_dYL0- sslmode=require");
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



	<! ========== HEADERWRAP ==================================================================================================== 
	=============================================================================================================================>
    <div id="headerwrap">
    	<div class="container">
			<div class="row centered">
				<div class="col-lg-8 col-lg-offset-2 mt">
					<h1>We are a Crowd Funding agency. We focus on Ideas, Awareness, and Execution.</h1>
					<a href="explore.php">
    					<p class="mt"><button type="button" class="btn btn-cta btn-lg">LEARN MORE</button></p>
    				</a>
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
				<!-- requires user to login to view projects for now will be changed  -->
				<a class="b-link-fade b-animate-go" href="#login" data-toggle="modal"><img width="350" src="assets/img/portfolio/port04.jpg" alt="" />
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
	
	<! ========== CALL TO ACTION 1 ============================================================================================== 
	=============================================================================================================================>    
    <div id="cta01">
    	<div class="container">
    		<div class="row">
    			<div class="col-lg-8 col-lg-offset-2">
    				<h2>The probability of success is difficult to estimate;<br/>but if we never try, the chance of success is zero.</h2>
    				<a href="explore.php">
    					<button type="button" class="btn btn-cta btn-lg">LEARN MORE</button>
    				</a>
    			</div>
    		</div><!-- /row -->
    	</div><!-- /container -->
    </div><! --/cta01 -->


	<! ========== FEATURED ICONS ================================================================================================ 
	=============================================================================================================================>    
    <div id="white">
	    <div class="container">
	    	<div class="row mt">
	    		<div class="col-lg-4 col-lg-offset-4 centered">
	    			<h3>Project Process</h3>
	    			<hr>
	    		</div>
	    	</div>
	    	<div class="row mt">
	    		<div class="col-lg-3">
	    			<p class="capitalize">1</p>
	    			<h4>Pitch</h4>
	    			<p>Present your idea to the crowd through a detailed description of the project.</p>
	    		</div>
	    		<div class="col-lg-3">
	    			<p class="capitalize">2</p>
	    			<h4>Fund</h4>
	    			<p>Backers are intrigued and funds your project.</p>
	    		</div>
	    		<div class="col-lg-3">
	    			<p class="capitalize">3</p>
	    			<h4>Execute</h4>
	    			<p>Make the project happen with the funds.</p>
	    		</div>    	
	
	    		<div class="col-lg-3">
	    			<p class="capitalize">4</p>
	    			<h4>Follow up/Rewards</h4>
	    			<p>Provide follow ups with the current situation of the project and give out rewards for the backers.</p>
	    		</div>
	    	</div><!-- /row -->
	    </div><!-- /container -->
    </div><!-- /white -->

		
	<! ========== QUICK STAT ================================================================================================= 
	=============================================================================================================================>    
	<div id="black">
		<div class="container pt">
			<div class="row mt centered">
				<div class="col-lg-3">
					<p><i class="fa fa-bolt"></i></p>
					<h1>3,675</h1>
					<hr>
					<h4>Projects Live</h4>
				</div>

				<div class="col-lg-3">
					<p><i class="fa fa-rocket"></i></p>
					<h1>2,102</h1>
					<hr>
					<h4>Projects Funded</h4>
				</div>

				<div class="col-lg-3">
					<p><i class="fa fa-user"></i></p>
					<h1>1,130</h1>
					<hr>
					<h4>Total Backers</h4>
				</div>

				<div class="col-lg-3">
					<p><i class="fa fa-repeat"></i></p>
					<h1>563</h1>
					<hr>
					<h4>Repeat Backers</h4>
				</div>

			</div><!-- /row -->
		</div><!-- /container -->
	</div><!-- /black -->

	<! ========== TESTIMONIAL CAROUSEL ========================================================================================== 
	=============================================================================================================================>    

	<div class="container">
    	<div class="row mt">
    		<div class="col-lg-4 col-lg-offset-4 centered">
    			<h3>Honest Testimonials</h3>
    			<hr>
    		</div>
    	</div><! --/row -->
	
		<div class="row mt">
			<div class="col-lg-8 col-lg-offset-2 centered">
				<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">				
					<!-- Wrapper for slides -->
					<div class="carousel-inner">
						<div class="item active">
						  <h2>Thanks to Funder Brand Name, my dreams became reality. It has been a crazy journey but we made it happen!</h2>
						  <h5>James Cameron - Ninjas on Titanic</h5>
						</div>
						
						<div class="item">
						  <h2>I did not expect to get such a great response from the community. Thanks for the support, we did it!</h2>
						  <h5>Ted Adams - CUPE 3902</h5>
						</div>
					</div><!-- /carousel-inner -->
				
				</div><! --/carousel-example -->		
			</div><!-- /col-lg-8 -->
		</div><! --/row -->
	</div><!-- /container -->

	
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

  	<script src="assets/js/signup.js"></script>
  	<script src="assets/js/signin.js"></script>
  </body>
</html>





