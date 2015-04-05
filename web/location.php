<!DOCTYPE html>
<html lang="en">
	<head>
		<?php 
			include("template/head.php");
			include("backend/checksession.php");
		?>
		<title>community</title>

	</head>
	<body>
		<?php include("template/loginnav.php"); ?>
		<div class="container">	
			<div class="row mt centered ">
				<div class="col-lg-4 col-lg-offset-4">
					<h3>Location Name</h3>
					<hr>
					<div class="row">
						<h2>Comments</h2>
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
			</div><!-- /row -->
		</div>	

		<?php include("template/footer.php"); ?>

	</body>
</html>