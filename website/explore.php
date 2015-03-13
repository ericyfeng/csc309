<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="assets/ico/favicon.png">

    <title>Explore</title>

    <link href="assets/css/hover_pack.css" rel="stylesheet">

    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="assets/css/main.css" rel="stylesheet">
    <link href="assets/css/colors/color-74c9be.css" rel="stylesheet">    
    <link href="assets/css/animations.css" rel="stylesheet">
    <link href="assets/css/font-awesome.min.css" rel="stylesheet">
    <link href="assets/css/explore.css" rel="stylesheet">       
    
    <!-- Main Jquery & Hover Effects. Should load first -->
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="assets/js/hover_pack.js"></script>
    

  </head>

  <body>
  	<?php
		//enable php debugging
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		date_default_timezone_set('America/Toronto');
		$dbconn = pg_connect("dbname=cs309 user=Daniel");
	?>

    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-fixed-top topnav" role="navigation">
        <div class="container topnav">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <a class="navbar-brand topnav" href="index.php">Brand Name</a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a href="#">Explore</a>
                    </li>
                    <li>
                        <a href="#signup" data-toggle="modal">Sign up</a>
                    </li>
                    <li>
                        <a href="#login" data-toggle="modal">Login</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>

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
						$featured = "select projid, goalamount, curramount, startdate, enddate, t1.description, location, popularity, rating, longdesc, community.description from 
							(select * from project natural join communityendorsement order by rating desc limit 6) t1, community where community.commid=t1.commid;";
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
	
	<div id="f">
		<div class="container">
			<div class="row">
				<!-- ADDRESS -->
				<div class="col-lg-3">
					<h4>Contact Us</h4>
					<p>
						<i class="fa fa-mobile"></i> +1-123-3902<br/>
						<i class="fa fa-envelope-o"></i> support@brandname.com
					</p>
				</div>  <!--/col-lg-3 -->
				

				<!-- LATEST POSTS -->
				<div class="col-lg-3">
					<h4>Latest Projects</h4>
					<p>
						<i class="fa fa-angle-right"></i> The Ninja Film<br/>
						<i class="fa fa-angle-right"></i> Water proof bananas<br/>
						<i class="fa fa-angle-right"></i> Strike Money<br/>
						<i class="fa fa-angle-right"></i> Smart Headphones<br/>
						<i class="fa fa-angle-right"></i> 50 shades of blue novel<br/>
					</p>
				</div><!-- /col-lg-3 -->
				
				<!-- NEW PROJECT -->
				<div class="col-lg-3">
					<h4>New Project</h4>
					<a href="#"><img class="img-responsive" src="assets/img/portfolio/port03.jpg" alt="" /></a>
				</div><!-- /col-lg-3 -->
				
				
			</div> <!--/row -->
		</div><!-- /container -->
	</div><!-- /f -->

	<! ========== MODAL ======================================================================================================== 
	=============================================================================================================================>    
    <div class="modal fade" id="signup" tabindex="-1" role= "dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4>Sign Up</h4>
				</div>
				<div class="modal-body">

						<div class="form-group">
							<label for="fname">First Name:</label>
							<input type="text" class="form-control" id="fname" name="fname" placeholder="Please Enter Your First Name">
						</div>
						<div class="form-group">
							<label for="text">Last Name:</label>
							<input type="text" class="form-control" id="lname" name="lname" placeholder="Please Enter Your Last Name">
						</div>				
						<div class="form-group">
							<label for="email">Email:</label>
							<input type="email" class="form-control" id="email" name="email" placeholder="Please Enter Your Email">
						</div>
						<div class="form-group">
							<label for="pwd">Password:</label>
							<input type="password" class="form-control" id="passwd" name="passwd"  placeholder="Please Enter Your Password">
						</div>
						<div class="form-group">
							<label for="repwd">Confirm Password:</label>
							<input type="password" class="form-control" id="confirm" name="confirm" placeholder="Please Re-Enter Your Password">
						</div>
						<div class="modal-footer">
				      		<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				      		<button type="button" class="btn btn-primary" onclick="regjax()">Sign Up!</button>
	  					</div>						

					<p id="status"></p>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="login" tabindex="-1" role= "dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4>Login</h4>
				</div>
				<div class="modal-body">
					<form action="login.php" method="POST">
						<div class="form-group">
							<label for="email">Email:</label>
							<input type="email" class="form-control" id="email" name="email" placeholder="Please Enter Your Email">
						</div>
						<div class="form-group">
							<label for="pwd">Password:</label>
							<input type="password" class="form-control" id="pwd" name="passwd" placeholder="Please Enter Your Password">
						</div>
						<div class="checkbox">
							<label><input type="checkbox">Remember Me</label>
						</div>
						<div class="extralogin">
							<a href="#">Forgot your password?</a>
							<p>Don't have an account?
								<a href="#signup" data-toggle="modal" data-dismiss="modal">Register now!</a>
							</p>	
						</div>
						<div class="modal-footer">
					      	<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					      	<input type="submit" class="btn btn-primary" value="Log In">
  						</div>
					</form>
				</div>

			</div>
		</div>
	</div>

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
