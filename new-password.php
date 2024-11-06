<?php require_once "controllerUserData.php"; ?>
<?php 
$email = $_SESSION['email'];
if($email == false){
	echo "<script>location.href = 'login-user.php';</script>";
	exit;
}
?>
<?php include_once 'header.php'; ?>
<body>
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-4 my-0 p-0 shadow" style="min-height: 100vh">
				<div class="d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 263px)">
					<img src="./public/assets/logo.png" alt="logo" style="width: 75%">
				</div>
				<form class="p-4 bg-white" action="new-password.php" method="POST" autocomplete="off">
					<h2 class="mb-4 font-weight-bold" style="font-size: 24px;">New Password</h2>
					<?php 
					if(isset($_SESSION['info'])){
						?>
						<div class="alert alert-success text-center">
							<?php echo $_SESSION['info']; ?>
						</div>
						<?php
					}
					?>
					<?php
					if(count($errors) > 0){
						?>
						<div class="alert alert-danger text-center">
							<?php
							foreach($errors as $showerror){
								echo $showerror;
							}
							?>
						</div>
						<?php
					}
					?>
					<div class="form-group">
						<input class="form-control" type="password" name="password" placeholder="Create new password" required>
					</div>
					<div class="form-group">
						<input class="form-control" type="password" name="cpassword" placeholder="Confirm your password" required>
					</div>
					<div class="form-group">
						<input class="btn btn-primary btn-block py-2 rounded" type="submit" name="change-password" value="Change">
					</div>
				</form>
			</div>
		</div>
	</div>

</body>
</html>