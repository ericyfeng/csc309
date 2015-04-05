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

  		$project = "select * from project natural join location where projid=$1";
  		pg_prepare($dbconn, "project", $project);
  		$result = pg_execute($dbconn, "project", array($projid));
  		$row = pg_fetch_array($result);

  	?>
    <title><?php echo $row["description"] ?></title>

    <!-- Custom styles for this template -->
    <link href="assets/css/project.css" rel="stylesheet"> 

	<script>
	//for ajax updating the current $$ amount after you donate for INSTANT GRATIFICATION
	function donate(projid, goal)
	{
		var donation = document.getElementById("donation").value;
		if(donation < 1)
		{
			return;
		}
		var newAmount = new XMLHttpRequest();
		newAmount.onreadystatechange= function ()
		{
			var resp = newAmount.responseText;
			document.getElementById("live_total").innerHTML="$"+resp;
			//document.getElementById("progress").style.width = resp / goal * 100;
		}
		newAmount.open("POST", "backend/addmoney.php", true);
		newAmount.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		newAmount.send("projid="+projid+"&amount="+donation);
	}

	function rate(projid)
	{
		var rating = document.getElementById("rating").value;
		var ajax = new XMLHttpRequest();
		ajax.onreadystatechange = function ()
		{
			document.getElementById("liverating").innerHTML=ajax.responseText;
		}
		ajax.open("POST", "backend/addrating.php", true);
		ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		ajax.send("projid="+projid+"&rating="+rating);
	}

	function urate(email, disp, rater)
	{
		var urating = document.getElementById(rater).value;
		var ajax = new XMLHttpRequest();
		ajax.onreadystatechange = function ()
		{
			document.getElementById(disp).innerHTML="("+ajax.responseText+")";
		}
		ajax.open("POST", "backend/addurating.php", true);
		ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		ajax.send("ratee="+email+"&urating="+urating);
	}

	function addcomment(pid, fname, lname)
	{
		var comment = document.getElementById("newcomment").value;
		var ajax = new XMLHttpRequest();
		var history = document.getElementById("commentHistory");
		//don't really need ajax here but this is a convenient way to send the info
		//	to the db and add the new comment in at the same time
		ajax.open("POST", "backend/addcomment.php", true);
		ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		ajax.send("pid="+pid+"&comment="+comment);

		var newc = document.createElement("li");
		newc.innerHTML="<h4>"+fname + " " + lname+"</h4><li><p>"+comment+"</p></li>";
		history.appendChild(newc);
	}

	//for ajax feedback on whether your attempt to add a person as an initiator worked
	function addinit(pid)
	{
		var email = document.getElementById("newinit").value;
		var ajax = new XMLHttpRequest();
		ajax.onreadystatechange = function ()
		{
			document.getElementById("status").innerHTML=ajax.responseText;
		}
			ajax.open("POST", "backend/addinit.php", true);
		ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		ajax.send("email="+email+"&pid="+pid);
	}

	//for ajax updating of your project's tag list
	function addtag(pid)
	{
		var commsel = document.getElementById("newtags");
		var commid = commsel.value;
		var ajax = new XMLHttpRequest();
		ajax.onreadystatechange = function ()
		{
			document.getElementById("taglist").innerHTML=ajax.responseText;
		}
		ajax.open("POST", "backend/addtag.php", true);
		ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		ajax.send("commid="+commid+"&pid="+pid);
	}
	</script>
  </head>

  <body>
	<! ========== NAV BAR ==================================================================================================== 
	=============================================================================================================================>

	<?php
		$owner;
		if($_SESSION["loggedin"] == 1)
		{
			include("template/loginnav.php");
			$ownertest = "select count(*) from project natural join initiator where email=$1 and projid=$2";
			pg_prepare($dbconn, "ownertest", $ownertest);
			$result5 = pg_execute($dbconn, "ownertest", array($_SESSION["email"], $projid));
			$row5 = pg_fetch_row($result5);
			if($row5[0] == 1)
			{
				$owner = 1;
			}
			else
			{
				$owner = 0;
			}
		}
		else
		{
			include("template/navbar.php");
			$ownere = 0;
		}
	?>

	<! ========== MAIN ======================================================================================================== 
	=============================================================================================================================> 

	<div class="container">
		<div class="row centered top-margin">
			<div class="col-sm-8 col-sm-offset-2">
				<h1><?php echo $row["description"] ?></h1>
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
						<h3 id="live_total">$<?php echo $row["curramount"] ?></h3>
						<p class="lead">Raised of <?php echo $row["goalamount"] ?> Goal</p>			
					</div>						
					<div class="progress">
						<?php 
							$progress = round(($row["curramount"] / $row["goalamount"]), 2) * 100;
							$enddate = new DateTime($row["enddate"]);
							$today = new DateTime(date("Y-m-d"));
							$remaining = date_diff($today, $enddate) ;
						?>
						<div id="progress" class="progress-bar progress-bar-theme" role="progressbar" aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $progress ?>%;">
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
					<p class="lead"><i class="fa fa-map-marker"></i> <?php echo $row["locname"] ?></p>

					<?php
						if($_SESSION["loggedin"] == 1)
						{
					?>
					<div class="form-group">
						<input type="text" id="donation" class="form-control">
						<button type="sub" class="btn btn-danger btn-lg btn-block" onclick="donate('<?= $projid?>', '<?= $row['goalamount']?>')">Fund the project!</button>
						</div>	    	

					<div class="form-group">
						<h4 class="rate">Rate Project (<span id="liverating"><?= $row['rating']?></span>):</h4>
						<select class="form-control" id="rating" name="rating">
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
						<button class="btn-theme" onclick="rate('<?= $projid?>')">submit</button>
					</div>
					<?php
						}
					?>
				</div>
			</div>
		</div>
		<hr>
		
		<div class="row">
		<?php
			if($owner == 1)
			{
		?>
			<a href="#" class="btn btn-info" data-toggle="modal" data-target="#addtag">
				<span class="fa fa-tag"></span> +
			</a>

			<a href="#" class="btn btn-info" data-toggle="modal" data-target="#addowner">
				<span class="fa fa-rocket"></span> +
			</a>
		<?php
			}
		?>
			<div class="tags" id="taglist">
				<?php
				$tags = "select description from communityendorsement natural join community where projid=$1";
				pg_prepare($dbconn, "tags", $tags);
				$result3 = pg_execute($dbconn, "tags", array($projid));
				while($row3 = pg_fetch_row($result3))
				{
				?>
					<span class="lead"><i class="fa fa-tag"></i> <?php echo $row3[0] ?> </span>
				<?php
				}
				?>
			</div>

		<?php
			$initinfo = "select * from initiator natural join users where projid=$1";
			pg_prepare($dbconn, "initinfo", $initinfo);
			$result4 = pg_execute($dbconn, "initinfo", array($projid));
			while($row4 = pg_fetch_array($result4))
			{
		?>

		<div class="p-initiator">
			<h4>Initiated by: </h4>
			<a href="#">
				<p class="lead"><i class="fa fa-rocket"></i> <?php echo $row4["fname"] . " " . $row4["lname"];?> <span id="liveurate<?= $row4['email']?>">(<?= $row4['reputation']?>)</span></p>
			</a>


		<?php
				if($_SESSION["loggedin"] == 1)
				{
		?>		
		<div class="form-group">
			<h4>Rate initiator:</h4>
			<select class="form-control" id="rater<?= $row4['email']?>">
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
			<button class="btn-theme" onclick="urate('<?= $row4['email']?>', 'liveurate<?= $row4['email']?>', 'rater<?= $row4['email']?>')">submit</button>
		</div>
		</div>
		<?php
				}
			}
		?>
		<p id="status"></p>
		<hr>


			<h2>MORE ABOUT PROJECT</h2>
			<p><?php echo $row["longdesc"] ?></p>


		<hr>

			<h2>Comments</h2>
				<label for="newcomment" maxlength="200">Comment on the project:</label>
				<textarea class="form-control" rows="4" id="newcomment"></textarea>
				<button class="btn-theme" onclick="addcomment('<?php echo $projid?>', '<?php echo $_SESSION['fname']?>', '<?php echo $_SESSION['lname']?>')">Add Comment</button>
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

		<!--Add owner popup-->
		<div class="modal fade" id="addowner" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
			<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4>Add Project Owner</h4>
				</div>
				<div class="modal-body">
					<input type="text" id="newinit"></input>
					<input type="button" class="btn btn-warning" onclick="addinit('<?php echo $projid?>')" value="+">
				</div>
				</div>
			</div>
		</div>

		<!--Add tag popup-->
		<div class="modal fade" id="addtag" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
			<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4>Add A Tag</h4>
				</div>
				<div class="modal-body">
					<select id="newtags" name="newtags">
						<?php
							$unused = "select * from community where (commid) not in (select commid from communityendorsement where projid=$1)";
							pg_prepare($dbconn, "unused", $unused);
							$result6 = pg_execute($dbconn, "unused", array($projid));
							while($row6 = pg_fetch_row($result6))
							{
								echo "<option value=\"$row6[0]\">$row6[1]</option>";
							}
						?>
					</select>
					<input type="button" class="btn btn-warning" onclick="addtag('<?php echo $projid?>')" value="+">
				</div>
				</div>
			</div>
		</div>

  
  </body>
</html>
