    <div class="modal fade" id="signup" tabindex="-1" role= "dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4>Sign Up</h4>
				</div>
				<div class="modal-body">

						<div class="form-group">
							<label for="fname">First Name:</label>
							<input type="text" class="form-control" name="fname">
						</div>
						<div class="form-group">
							<label for="text">Last Name:</label>
							<input type="text" class="form-control" name="lname">
						</div>				
						<div class="form-group">
							<label for="email">Email:</label>
							<input type="email" class="form-control" name="email">
						</div>
						<div class="form-group">
							<label for="pwd">Password:</label>
							<input type="password" class="form-control" name="password">
						</div>
						<div class="form-group">
							<label for="repwd">Confirm Password:</label>
							<input type="password" class="form-control" name="confirm" id="confirm">
						</div>
						<p id="reg-status"></p>
						<div class="modal-footer">
				      		<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				      		<button type="button" class="btn btn-primary" id="register">Sign Up!</button>
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
					<form action="../backend/login.php" method="POST">
						<div class="form-group">
							<label for="email">Email:</label>
							<input type="text" class="form-control" name="email" placeholder="Please Enter Your Email">
						</div>
						<div class="form-group">
							<label for="pwd">Password:</label>
							<input type="password" class="form-control" name="password" placeholder="Please Enter Your Password">
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
						<p id="login-status"></p>
						<div class="modal-footer">
					      	<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					      	<input type="submit" class="btn btn-primary" value="Log In"></input>
  						</div>
					</form>
				</div>

			</div>
		</div>
	</div>
