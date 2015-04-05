	<! ========== NAV BAR ==================================================================================================== 
	=============================================================================================================================>

    <nav class="navbar navbar-default navbar-fixed-top topnav" role="navigation">
        <div class="container topnav">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <a class="navbar-brand topnav" href="index.php">HOME</a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <?php
                        if($_SESSION["admin"] == 1) {
                    ?>
                    <!--Admin button-->
                    <li>
                        <a href="admin2.php?sessid=<?php echo $sessid?>">Administration</a>
                    </li>
                    <?php 
                        }
                    ?>                
                    <li>
                        <a href="../explore.php">Explore</a>
                    </li>
                    <li>
                        <a href="../newproject.php">New Project</a>
                    </li>
                    <li>
                        <a href="../dashboard.php">Dashboard</a>
                    </li>                    
                    <li>
                        <a href="../profile.php">Profile</a>
                    </li>
                    <li>
                        <a href="../logout.php">Log out</a>
                    </li>                    
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>
