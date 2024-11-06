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
				<div class="d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 223px)">
					<img src="./public/assets/logo.png" alt="logo" style="width: 75%">
				</div>
				<form class="p-4 bg-white" action="reset-code.php" method="POST" autocomplete="off">
					<h2 class="mb-4 font-weight-bold">Code Verification</h2>
					<?php 
					if(isset($_SESSION['info'])){
						?>
						<div class="alert alert-success text-center" style="padding: 0.4rem 0.4rem">
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
						<input class="form-control" type="number" name="otp" placeholder="Enter code" required>
					</div>
					<div class="form-group">
						<input class="btn btn-primary btn-block py-2 rounded" type="submit" name="check-reset-otp" value="Submit">
					</div>
				</form>
			</div>
		</div>
	</div>

	<script>
		let otp = document.getElementById("otp");
		function checkLength(num) {
			console.log(num.toString());
			if(num.toString().length <= 0 || num.toString().length < 6 || num.toString().length > 6) {
				otp.classList.add('is-invalid');
				otp.classList.remove('is-valid');
			} if(num.toString().length == 6) {
				otp.classList.remove('is-invalid');
				otp.classList.add('is-valid');
			}
		}
	</script>
</body>
</html>