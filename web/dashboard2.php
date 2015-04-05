<!DOCTYPE html>
<html lang="en">
  <head>
  	<?php 
  		include("template/head.php"); 
  		include("backend/checksession.php");
		$useremail = "select email from session where sessionid=$1";
  		pg_prepare($dbconn, "useremail", $useremail);
  		$result = pg_execute($dbconn, "useremail", array($sessid));
  		$row = pg_fetch_row($result);
  		$email = $row[0];

  	?>
    <title>Dashboard</title>

    <link href="assets/css/explore.css" rel="stylesheet">     
		<script>
		//performs the filtering of other peoples's projects when you click the red filter by tag button
		function filtertags(comm_requested)
		{

			var blacklist = document.getElementsByClassName("commgeneric");
			var j;
			for(j=0; j<blacklist.length; j++)
			{
				blacklist[j].style.display = "none";
			}
			
			var fixlist = document.getElementsByClassName(comm_requested);
			for(j=0; j<fixlist.length; j++)
			{
				fixlist[j].style.display = "table-row";
			}
		}

		function rm(pid, sessid)
		{
			var ajax = new XMLHttpRequest();
			ajax.onreadystatechange = function ()
			{
				document.getElementById(pid).innerHTML=ajax.responseText;
			}
			ajax.open("POST", "rmproj.php", true);
			ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			ajax.send("sessid="+sessid+"&pid="+pid);
		}

		function redirect(mode)
		{
			if(mode == 'comm')
			{
				window.location.replace("community.php?commid="+document.getElementById("commsel").value);
			}
			else if (mode == 'loc')
			{
				window.location.replace("location.php?locid="+document.getElementById("locsel").value);
			}
		}
		</script>
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


		<! ========== PROJECTS ==================================================================================================== 
		=============================================================================================================================>    

		<!--Everything in the body will be in a single row-->
		<div class="row">
				<div class="container">	
					<div class="row mt centered ">
						<div class="col-lg-4 col-lg-offset-4">
							<h3>My Projects</h3>
							<hr>
						</div>
					</div><!-- /row -->

					<div class="row mt">
					<?php
						//for now just pull any project in the backend to display
						$myproj = "select projid, goalamount, curramount, startdate, enddate, t1.description, locname, popularity, rating, longdesc, community.description, t1.email from (select * from project natural join communityendorsement natural join location natural join initiator order by rating desc limit 6) t1, community where community.commid=t1.commid and email=$1";
						pg_prepare($dbconn, "myproj", $myproj);
						$result = pg_execute($dbconn, "myproj", array($email));
						while ($row = pg_fetch_row($result)) {
					?>
						<?php 
							$progress = round(($row[2] / $row[1]), 2) * 100;
							$enddate = new DateTime($row[4]);
							$today = new DateTime(date("Y-m-d"));
							$remaining = date_diff($today, $enddate) ;
						?>
						<div class="col-lg-4 col-md-4 col-xs-12 desc">
							<a class="b-link-fade b-animate-go" href="project.php?projid=<?php echo $row[0]?>"><img width="350" src="assets/img/portfolio/port04.jpg" alt="" />
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

							<button class="btn btn-danger btn-lg btn-block" onclick="rm(<?php echo $row[0] . ", " . $sessid ;?>)">Delete</button>		
							
							<hr-d>
							<p class="time"><i class="fa fa-tag"></i> <?= $row[10] ?> | <i class="fa fa-calendar"></i> <?= $remaining->days ?> days left | <i class="fa fa-map-marker"></i> <?= $row[6] ?></p>
						</div><!-- col-lg-4 -->
						<?php
							}
						?>	
					</div><!-- /row -->
				</div><!-- /container -->
		</div><!-- body row -->
		<div class="row">
			<div class="container">	
				<div class="row mt centered ">
					<div class="col-lg-4 col-lg-offset-4">
						<h3>Connect!</h3>
						<hr>
							<h4>Communities:</h4>
							<select id="commsel" class="form-control">
								<?php 
									$listcomm = "select commid, description from personalinterests natural join community where email=$1";
									pg_prepare($dbconn, "listcomm", $listcomm);
									$result = pg_execute($dbconn, "listcomm", array($email));
									while ($row = pg_fetch_array($result)) {
								?>
										<option value="<?php echo $row['commid'] ?>">
											<?php echo $row["description"] ?>
										</option>
								<?php } ?>
							</select>
							<button onclick="redirect('comm')">Go!</button>

							<h4>Locations:</h4>
							<select id="locsel" class="form-control">
								<?php 
									$listloc = "select locid, locname from location";
									pg_prepare($dbconn, "listloc", $listloc);
									$result = pg_execute($dbconn, "listloc", array());
									while ($row = pg_fetch_array($result)) {
								?>
										<option value="<?php echo $row['locid'] ?>"><?php echo $row["locname"] ?></option>
								<?php } ?>
							</select>
							<button onclick="redirect('loc')">Go!</button>
					</div>
				</div><!-- /row -->
		</div>


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
