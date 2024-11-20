<?php include_once "controllerUserData.php"; ?>
<?php include_once "header.php"; ?>
<?php include_once "navbar.php"; ?>

<body>
	<!-- BACKGROUND GIF -->
	<!-- <img id="gif" src="./public/assets/43295-heart-fly-transparent-bg.gif" alt="" > -->


	<!-- LOADER -->
	<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
	<div class="loader-container">
		<div>
			<lottie-player id="lottie-player-heart" src="./public/assets/lf30_editor_drzgxbyf.json"  background="transparent"  speed="1.2"  style="width: 300px; height: 300px;" loop autoplay></lottie-player>
		</div>
	</div>


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
		<div class="wrapper-chat">
			<section class="chat-area">
				<header>
					<?php 
					$user_id = mysqli_real_escape_string($con, $_GET['user_id']);
					$sql = mysqli_query($con, "SELECT * FROM usertable ut, userprofile up WHERE ut.uid=up.uid and unique_id = {$user_id}");
					if(mysqli_num_rows($sql) > 0){
						$row = mysqli_fetch_assoc($sql);
					}else{
						echo "<script>location.href = 'users.php';</script>";
						exit;
					}
					?>
					<a href="users.php" class="back-icon"><i class="fas fa-arrow-left"></i></a>
					<img src="<?= profile_photo($row['profile_photo'],$row['gender']) ; ?>" alt="">
					<div class="details">
						<span><?php echo $row['name']; ?></span>
						<p><?php echo $row['active']; ?></p>
					</div>
				</header>
				<div class="chat-box">

				</div>
				<form action="#" class="typing-area">
					<input type="text" class="incoming_id" name="incoming_id" value="<?php echo $user_id; ?>" hidden>
					<input type="text" name="message" class="input-field" placeholder="Type a message here..." autocomplete="off">
					<button><i class="fab fa-telegram-plane"></i></button>
				</form>
			</section>
		</div>
	</div>


	<!-- POPOVERS -->
	<ul id="popover-content-profile-icon" class="list-group" style="display: none;">
		<span class="list-group-item btn btn-outline-success rounded"><a href="edit-profile.php">Edit profile</a></span>
		<div class="dropdown-divider"></div>
		<span class="list-group-item btn btn-outline-danger rounded"><a href="php/logout.php?logout_id=<?php echo $_SESSION['unique_id']; ?>" class="">Logout</a></span>
	</ul>

	<ul id="popover-content-matches" class="list-group" style="display: none;">
		<?php if(count($matches) == 0) { ?>
			<li class="list-group-item btn btn-light text-left d-flex flex-column pl-2 pb-0 mb-2 rounded disabled"><b>Sorry ☹</b><p class="font-weight-light">No matches yet.....</p></li>
		<?php } else { ?>  
			<?php for($i = 0; $i < count($matches); $i++) { ?>
				<li class="match-li list-group-item btn btn-light text-left d-flex flex-column pl-2 pb-0 mb-2 <?php if($i == 0) echo 'rounded-top'; elseif($i == count($matches) - 1) echo 'rounded-bottom'; elseif($i == 0 && $i == count($matches) - 1) echo "rounded"; ?>" style="cursor: pointer;"><b class="match-li-b"><?php echo $matches[$i]['name']; ?>, <?php echo $matches[$i]['age']; ?></b><p class="match-li-p font-weight-light">Matched <?php echo $matches[$i]['date']; ?></p><span class="d-none"><?php echo $matches[$i]['id']; ?></span></li>
			<?php } ?>  
		<?php } ?>  
	</ul>

	<ul id="popover-content-notifs" class="list-group" style="display: none;">
		<?php if(count($notifs_content_unseen) != 0) { ?>
			<?php for($i = 0; $i < count($notifs_content_unseen); $i++) { ?>  
				<li class="list-group-item btn btn-light text-left d-flex flex-column pl-2 pb-0 mb-2 <?php if($i == 1) echo "rounded-top" ?>"><p class="notif-text mb-0"><?php echo $notifs_content_unseen[$i] ?></p><p class="font-weight-light notif-date"><?php echo $notifs_date_unseen[$i] ?></p></li>
			<?php } ?>
			<div class="dropdown-divider"></div>
		<?php } ?>
		<?php if(count($notifs_content_seen) != 0) {  ?>
			<?php for($i = 0; $i < count($notifs_content_seen); $i++) { ?>  
				<li class="list-group-item btn btn-light text-left d-flex flex-column pl-2 pb-0 mb-2 notif-old <?php if($i == count($notifs_content_seen) - 1) echo "rounded-bottom" ?>"><p class="notif-text mb-0"><?php echo $notifs_content_seen[$i] ?></b><p class="font-weight-light notif-date"><?php echo $notifs_date_seen[$i] ?></p></li>
			<?php } ?>
		<?php } ?>  
	</ul>


	<!-- SCRIPTS -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	<script>
  //to activate profile icon popover
  $(function() {
  	$('[data-toggle="popover-profile-icon"]').popover({
  		html: true,
  		content: function() {
  			return $('#popover-content-profile-icon').html();
  		}
  	});
  });

    //to activate matches popover
    $(function() {
    	$('[data-toggle="popover-matches"]').popover({
    		html: true,
    		content: function() {
    			return $('#popover-content-matches').html();
    		}
    	});
    });

    //to activate notifs popover
    $(function() {
    	$('[data-toggle="popover-notifs"]').popover({
    		html: true,
    		content: function() {
    			return $('#popover-content-notifs').html();
    		}
    	});
    });
</script>
<script src="./js/home.js"></script>
<script src="javascript/chat.js"></script>

</body>
</html>
