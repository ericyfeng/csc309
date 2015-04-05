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
  		$project = "select projid, goalamount, curramount, startdate, enddate, t1.description as title, locname, popularity, rating, longdesc, community.description, fname, lname, reputation from 
							(select * from project natural join communityendorsement natural join location natural join initiator natural join users where projid=$1) t1, community where community.commid=t1.commid;";

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
		<div class="row centered">
			<div class="col-sm-8 col-sm-offset-2">
				<h1><?php echo $row["title"] ?></h1>
				<hr>
			</div>
		</div><!-- /row -->

		<div class="row">
			<div class="col-lg-8 col-md-8 col-xs-12">
				<iframe width="720" height="483" src="https://www.youtube.com/embed/BEtIoGQxqQs" frameborder="0" allowfullscreen></iframe>
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
					<p class="lead"><i class="fa fa-user"></i> 420 Funders (needs implementation)</p>
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

	<! ========== MODALS ======================================================================================================== 
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
