<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="shortcut icon" href="assets/ico/favicon.png">

		<title>Brand Name</title>

		<link href="assets/css/hover_pack.css" rel="stylesheet">

		<!-- Bootstrap core CSS -->
		<link href="assets/css/bootstrap.css" rel="stylesheet">

		<!-- Custom styles for this template -->
		<link href="assets/css/main.css" rel="stylesheet">
		<link href="assets/css/colors/color-74c9be.css" rel="stylesheet">    
		<link href="assets/css/animations.css" rel="stylesheet">
		<link href="assets/css/font-awesome.min.css" rel="stylesheet">


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
		$dbconn = pg_connect("dbname=cs309 user=eric");
	?>

	<! ========== NAV BAR ==================================================================================================== 
	=============================================================================================================================>

    <nav class="navbar navbar-default navbar-fixed-top topnav" role="navigation">
        <div class="container topnav">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <a class="navbar-brand topnav" href="index.html">Brand Name</a>
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


	<! ========== HEADERWRAP ==================================================================================================== 
	=============================================================================================================================>
    <div id="headerwrap">
    	<div class="container">
			<div class="row centered">
				<div class="col-lg-8 col-lg-offset-2 mt">
					<h1>We are a Crowd Funding agency. We focus on Ideas, Awareness, and Execution.</h1>
    				<p class="mt"><button type="button" class="btn btn-cta btn-lg">LEARN MORE</button></p>
				</div>
				
			</div><!-- /row -->
    	</div><!-- /container -->
    </div> <!-- /headerwrap -->

	<! ========== BLOG POSTS ==================================================================================================== 
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
			$featured = "select * from project order by rating desc limit 6;";
			pg_prepare($dbconn, "featured", $featured);
			$result = pg_execute($dbconn, "featured", array());
			while ($row = pg_fetch_row($result)) {
		?>
			<?php 
				$progress = round(($row[2] / $row[1]), 2) * 100;
			?>
			<div class="col-lg-4 col-md-4 col-xs-12 desc">
				<a class="b-link-fade b-animate-go" href="#"><img width="350" src="assets/img/portfolio/port04.jpg" alt="" />
					<div class="b-wrapper">
					  	<h4 class="b-from-left b-animate b-delay03"> <?= $row[5] ?></h4>
					  	<p class="b-from-right b-animate b-delay03">Read More.</p>
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
				<p class="time"><i class="fa fa-tag"></i> Technology | <i class="fa fa-comment-o"></i> 3 | <i class="fa fa-calendar"></i> 14 Nov. | <i class="fa fa-map-marker"></i> <?= $row[6] ?></p>

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
    				<button type="button" class="btn btn-cta btn-lg">LEARN MORE</button>
    			</div>
    		</div><!-- /row -->
    	</div><!-- /container -->
    </div><! --/cta01 -->


	<! ========== BRANDS & CLIENTS =============================================================================================== 
	=============================================================================================================================>    
	<div id="grey">
		<div class="container">
			<div class="row mt centered ">
				<div class="col-lg-4 col-lg-offset-4">
					<h3>Brands & Clients</h3>
					<hr>
				</div><!-- /col-lg-4 -->
			</div><!-- /row -->
			
			<div class="row centered">
				<div class="col-lg-3 pt">
					<img class="img-responsive" src="assets/img/clients/client01.png" alt="">
				</div>
				<div class="col-lg-3 pt">
					<img class="img-responsive" src="assets/img/clients/client02.png" alt="">
				</div>
				<div class="col-lg-3 pt">
					<img class="img-responsive" src="assets/img/clients/client03.png" alt="">
				</div>
				<div class="col-lg-3 pt">
					<img class="img-responsive" src="assets/img/clients/client04.png" alt="">
				</div>

			</div><!-- /row -->
		</div><!-- /container -->
	</div><!-- /grey -->

	
	<! ========== BLACK SECTION ================================================================================================= 
	=============================================================================================================================>    
	<div id="black">
		<div class="container">
			<div class="row mt centered">
				<div class="col-lg-4 col-lg-offset-4">
					<h3>Our Work Process</h3>
					<hr>
				</div><!-- /col-lg-4 -->
			</div><!-- /row -->
			
			<div class="row mt">
				<div class="col-lg-8 col-lg-offset-2">
					<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
				</div><! --/col-lg-8 -->
			</div><!-- /row -->
		</div><!-- /container -->
	</div><!-- /black -->


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
	    			<p>Built for all levels of expertise, whether you need simple pages or complex ones, creating something incredible with Marco is an effortless and intuitive process.</p>
	    		</div>
	    		<div class="col-lg-3">
	    			<p class="capitalize">2</p>
	    			<h4>Fund</h4>
	    			<p>We’ve taken great care to ensure that Marco is fully retina-ready. So it’ll look good on any retina display. We use retina.js to ensure the best view.</p>
	    		</div>
	    		<div class="col-lg-3">
	    			<p class="capitalize">3</p>
	    			<h4>Execute</h4>
	    			<p>Marco fits any device handsomely. We tested our theme in major devices and browsers. Check it out and test it before buy it on responsinator.com.</p>
	    		</div>    	
	
	    		<div class="col-lg-3">
	    			<p class="capitalize">4</p>
	    			<h4>Follow up</h4>
	    			<p>Good looking animations are an essential part of the new theme design trend. We add animations.css, a cool script to help you enhance your site with style.</p>
	    		</div>
	    	</div><!-- /row -->
	    </div><!-- /container -->
    </div><!-- /white -->

	<! ========== CALL TO ACTION 2 ============================================================================================== 
	=============================================================================================================================>    
    <div id="cta02">
    	<div class="container">
    		<div class="row">
    			<div class="col-lg-8 col-lg-offset-2">
    				<h2>Start your own project today and fullfill your dreams today with us!</h2>
    				<button type="button" class="btn btn-cta btn-lg">LEARN MORE</button>
    			</div>
    		</div><!-- /row -->
    	</div><!-- /container -->
    </div><! --/cta02 -->

	<! ========== FEATURED ICONS ================================================================================================ 
	=============================================================================================================================>    
    <div class="container">
    	<div class="row mt">
    		<div class="col-lg-4 centered si">
    			<i class="fa fa-flask"></i>
    			<h4>Built with Bootstrap 3</h4>
    			<p>Built for all levels of expertise, whether you need simple pages or complex ones, creating something incredible with Marco is an effortless and intuitive process.</p>
    		</div>
    		<div class="col-lg-4 centered si">
    			<i class="fa fa-eye"></i>
    			<h4>Retina Display Theme</h4>
    			<p>We’ve taken great care to ensure that Marco is fully retina-ready. So it’ll look good on any retina display. We use retina.js to ensure the best view.</p>
    		</div>
    		<div class="col-lg-4 centered si">
    			<i class="fa fa-mobile"></i>
    			<h4>Responsive Design Always</h4>
    			<p>Marco fits any device handsomely. We tested our theme in major devices and browsers. Check it out and test it before buy it on responsinator.com.</p>
    		</div>    	

    		<div class="col-lg-4 centered si">
    			<i class="fa fa-cog"></i>
    			<h4>Really Nice Animations</h4>
    			<p>Good looking animations are an essential part of the new theme design trend. We add animations.css, a cool script to help you enhance your site with style.</p>
    		</div>
    		<div class="col-lg-4 centered si">
    			<i class="fa fa-flag"></i>
    			<h4>Font Awesome Included</h4>
    			<p>Font Awesome is the most used icon font on Bootstrap. Gives you scalable vector icons that can instantly be customized with the power of CSS.</p>
    		</div>
    		<div class="col-lg-4 centered si">
    			<i class="fa fa-heart"></i>
    			<h4>Carefully Crafted</h4>
    			<p>We aim to design both, functional & beautiful themes. Details are an important part of our main concept. We work hard to keep our code and front-end flawless.</p>
    		</div>    	
    	</div><!-- /row -->
    </div><!-- /container -->
    
	
	
	<! ========== BLACK SECTION ================================================================================================= 
	=============================================================================================================================>    
	<div id="black">
		<div class="container pt">
			<div class="row mt centered">
				<div class="col-lg-3">
					<p><i class="fa fa-instagram"></i></p>
					<h1>21,337</h1>
					<hr>
					<h4>Projects Live</h4>
				</div>

				<div class="col-lg-3">
					<p><i class="fa fa-music"></i></p>
					<h1>9,764</h1>
					<hr>
					<h4>Projects Funded</h4>
				</div>

				<div class="col-lg-3">
					<p><i class="fa fa-trophy"></i></p>
					<h1>107</h1>
					<hr>
					<h4>Some other stat</h4>
				</div>

				<div class="col-lg-3">
					<p><i class="fa fa-ticket"></i></p>
					<h1>209</h1>
					<hr>
					<h4>Other stat</h4>
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
						  <h2>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever.</h2>
						  <h5>Paul Morrison - BlackTie.co</h5>
						</div>
						
						<div class="item">
						  <h2>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.</h2>
						  <h5>Mike Wellington - BlackTie.co</h5>
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
				<a href="#"><h4>Are You Ready For The Next Step?</h4></a>
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
					<h4>Our Studio</h4>
					<p>
						Some Ave. 987,<br/>
						Postal 64733<br/>
						London, UK.<br/>
					</p>
					<p>
						<i class="fa fa-mobile"></i> +55 4893.8943<br/>
						<i class="fa fa-envelope-o"></i> hello@yourdomain.com
					</p>
				</div><! --/col-lg-3 -->
				

				<!-- LATEST POSTS -->
				<div class="col-lg-3">
					<h4>Latest Posts</h4>
					<p>
						<i class="fa fa-angle-right"></i> A post with an image<br/>
						<i class="fa fa-angle-right"></i> Other post with a video<br/>
						<i class="fa fa-angle-right"></i> A full width post<br/>
						<i class="fa fa-angle-right"></i> We talk about something nice<br/>
						<i class="fa fa-angle-right"></i> Yet another single post<br/>
					</p>
				</div><!-- /col-lg-3 -->
				
				<!-- NEW PROJECT -->
				<div class="col-lg-3">
					<h4>New Project</h4>
					<a href="#"><img class="img-responsive" src="assets/img/portfolio/port03.jpg" alt="" /></a>
				</div><!-- /col-lg-3 -->
				
				
			</div><! --/row -->
		</div><!-- /container -->
	</div><!-- /f -->
	
	<!--- MODAL -->
    <div class="modal fade" id="signup" tabindex="-1" role= "dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4>Sign Up</h4>
				</div>
				<div class="modal-body">
					<form id="logincheck">
						<div class="form-group">
							<label for="fname">First Name:</label>
							<input type="text" class="form-control" id="fname" placeholder="Please Enter Your First Name">
						</div>
						<div class="form-group">
							<label for="text">Last Name:</label>
							<input type="text" class="form-control" id="lname" placeholder="Please Enter Your Last Name">
						</div>				
						<div class="form-group">
							<label for="email">Email:</label>
							<input type="email" class="form-control" id="email" placeholder="Please Enter Your Email">
						</div>
						<div class="form-group">
							<label for="pwd">Password:</label>
							<input type="password" class="form-control" id="pwd" placeholder="Please Enter Your Password">
						</div>
						<div class="form-group">
							<label for="repwd">Confirm Password:</label>
							<input type="password" class="form-control" id="repwd" placeholder="Please Re-Enter Your Password">
						</div>
					</form>
					<div class="modal-footer">
			      		<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			      		<button type="button" class="btn btn-primary">Submit</button>
  					</div>
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
