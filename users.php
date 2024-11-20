<?php 
include_once "controllerUserData.php";
?>

<?php include_once "header.php"; ?>
<?php include_once "navbar.php"; ?>

<!-- HIDDEN INPUTS  -->
<input type="text" class="uid1-name" hidden value="<?php echo $name; ?>">
<input type="text" class="uid" hidden value="<?php echo $uid; ?>">


<!-- REDIRECT TO MATCH FORM -->
<form action="match.php" method="POST" id="match-redirect-form" hidden>
	<input type="text" id="match-redirect-uid1" name="match-redirect-uid1" value="<?php echo $uid; ?>">
	<input type="text" id="match-redirect-uid2" name="match-redirect-uid2">
</form>


<!-- CHAT USERS -->
<div class="wrapper-container">
	<div class="wrapper">
		<section class="users">
			<header>
				<div class="content">
					<?php 
					$sql1 = mysqli_query($con, "SELECT * FROM usertable WHERE unique_id = {$_SESSION['unique_id']}");
					if(mysqli_num_rows($sql1) > 0){
						$row1 = mysqli_fetch_assoc($sql1);
					}
					$uid = $row1['uid'];
					$sql = mysqli_query($con, "SELECT * FROM userprofile WHERE uid = '$uid'");
					if(mysqli_num_rows($sql) > 0){
						$row = mysqli_fetch_assoc($sql);
					}
					?>
					<img src="<?= profile_photo($row['profile_photo'],$row['gender']); ?>" alt="">
					<div class="details">
						<span><?php echo ucwords($row['name']) ?></span>
						<p><?php echo $row1['active']; ?></p>
					</div>
				</div>
				<a href="php/logout.php?logout_id=<?php echo $row1['unique_id']; ?>" class="logout">Logout</a>
			</header>
			<div class="search">
				<span class="text">Select an user to start chat</span>
				<input type="text" placeholder="Enter name to search...">
				<button><i class="fas fa-search"></i></button>
			</div>
			<div class="users-list"></div>
		</section>
	</div>
</div>  



<!-- SCRIPTS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="./js/home.js"></script>
<script src="javascript/users.js"></script>

</body>
</html>
